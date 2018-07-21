<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module\REST;
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

class Api extends \Codeception\Module
{
    protected $requiredFields = ['schemePath'];
    protected $config = ['schemePath' => ''];

    public function getAuthToken() {
        $restModule = $this->getModule('REST');

        $restModule->haveHttpHeader('Content-Type',  'application/json');
        $restModule->sendPOST('users/token', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'login' => USER_LOGIN, 'password' => USER_PASSWORD
                ]
            ]
        ]);

        $resp = json_decode($restModule->grabResponse(), true);
        return $resp['data']['attributes']['token'];
    }

    public function seeResponseJsonHasAttributes(array $attrs) {
        $restModule = $this->getModule('REST');
        $restModule->seeResponseIsJson();

        $xpath = '//data/attributes/';

        foreach ($attrs as $attr) {
            $restModule->seeResponseJsonMatchesXpath($xpath . $attr);
        }
    }

    public function seeResponseJsonFitsScheme($scheme) {
        $restModule = $this->getModule('REST');
        $basePath = $restModule->_getConfig('schemePath');

        $resp = json_decode($restModule->grabResponse());
        $validator = new Validator; $validator->validate(
            $resp,
            (object)['$ref' => 'file://' . realpath($basePath . $scheme)],
            Constraint::CHECK_MODE_COERCE_TYPES
        );

        $res = $validator->isValid();
        $err_msg = '';

        if (!$res) {
            foreach ($validator->getErrors() as $error) {
                $err_msg .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
        }

        $this->assertTrue($res, $err_msg);
    }

    public function seeResponseJsonMatchesSchema($schema) {
        /**
         * @var REST $restModule
         */
        $restModule = $this->getModule('REST');
        $restModule->seeResponseIsJson();

        $json = json_decode($schema, true);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($json));
        $result = [];

        foreach ($iterator as $value) {
            $keys = [];

            foreach (range(0, $iterator->getDepth()) as $depth) {
                $nextkey = $iterator->getSubIterator($depth)->key();
                if ($nextkey) {
                    $keys[] = $nextkey;
                }
            }

            $result[] = join('/', $keys);
        }

        $this->debug($result);
        foreach ($result as $path) {
            $restModule->seeResponseJsonMatchesXpath('//' . $path);
        }
    }
}
