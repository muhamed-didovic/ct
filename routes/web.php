<?php

use chillerlan\QRCode\{Output\QRImage, Output\QRMarkup, Output\QROutputAbstract, QRCode, QROptions};

class CO extends QRMarkup
{
    /**
     * @link https://github.com/codemasher/php-qrcode/pull/5
     *
     * @return string|bool
     */
    protected function svg(): string
    {
        if (!empty($_GET['size'])){
            //TODO: error handling
            $scale  = $_GET['size'] / $this->moduleCount / 2;//$this->options->scale;
            $length = $_GET['size'] ;
        } else {
            $scale  = 5;//$this->options->scale;
            $length = $this->moduleCount * $scale;
        }
        $moveQrFromTop = ($length*65)/100;
//
        $matrix = $this->matrix->matrix();
        //dd($length/2);
        $svg = '<svg
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                version="1.1"

                width="'.$length.'px"
                height="'.$length.'px"
                >'
               . $this->options->eol
               . '<defs>' .
               $this->options->svgDefs .
               '</defs>
                    <style>
                        /*#qr_group{transform:rotate(45deg);}*/
                    </style>
                    <g id="qr_group" transform="translate(0, '.($length-$moveQrFromTop).') rotate(45 '.($length/2).' '.($length/2).')">'
               . $this->options->eol;//translate(297,112.5)
        //rotate(45 ' . ($length/2) .' ' . ($length/2) .')
        foreach ($this->options->moduleValues as $M_TYPE => $value) {

            // fallback
            if (is_bool($value)) {
                $value = $value ? '#000' : '#fff';
            }

            $path   = '';
            $circle = '';

            foreach ($matrix as $y => $row) {
                //we'll combine active blocks within a single row as a lightweight compression technique
                $start = null;
                $count = 0;
                //dd($row);
                foreach ($row as $x => $module) {
                    //dd($module);
                    //dd($start, $module, $M_TYPE);
                    if ($module === $M_TYPE) {
                        $count++;

                        if ($start === null) {
                            $start = $x * $scale;
                        }

                        if ($row[$x + 1] ?? false) {
                            continue;
                        }
                    }

                    if ($count > 0) {
                        $len = $count * $scale;
                        $circleHalfRadius   = $scale / 2;
                        //dd($c);
                        //$path .= 'M' .$start. ' ' .($y * $scale). ' h'.$len.' v'.$scale.' h-'.$len.'Z ';

                        $yaxis = $start;
                        $limit = $len;
                        while ($start < $yaxis + $limit) {

                            $path .= 'M' . ($start += $scale) . ' ' . ($y * $scale) . ' m -' . $circleHalfRadius . ', 0 a ' . $circleHalfRadius . ',' . $circleHalfRadius . ' 0 1,0 ' . ($circleHalfRadius * 2) . ',0 a ' . $circleHalfRadius . ',' . $circleHalfRadius . ' 0 1,0 -' . ($circleHalfRadius * 2) . ',0 ';
                            //$yaxis +=5;
                        }
                        // reset count
                        $count = 0;
                        $start = null;
                    }
                }
            }

            if (!empty($path)) {
                $svg .= '<path class="qr-' . $M_TYPE . ' ' . $this->options->cssClass . '" stroke="transparent" fill="' . $value . '" fill-opacity="' . $this->options->svgOpacity . '" d="' . $path . '" />' . $circle;
            }

        }

        // close svg
        $svg .= '</g>
<image xlink:href="circle.png" width="'.$length.'"/>
<!--<image xlink:href="eye.png" x="' .($length/2-21). '" y="' .($length/2-21). '" width="42px"/>-->
</svg>' . $this->options->eol;

        // if saving to file, append the correct headers
        if ($this->options->cachefile) {
            return '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . $this->options->eol . $svg;
        }

        return $svg;
    }
}

class MyCustomOutput extends QROutputAbstract
{
    /**
     * {@inheritDoc}
     */
    public function dump(?string $file = NULL)
    {
        $output = '';
        for ($row = 0; $row < $this->moduleCount; $row++) {
            for ($col = 0; $col < $this->moduleCount; $col++) {
                $output .= (int)$this->matrix->check($col, $row);

                // boolean check a module
                //                if($this->matrix->check($col, $row)){ // if($module >> 8 > 0)
                //                    // do stuff, the module is dark
                //                    //$output .= "<circle cx='{$col}' cy='{$row}' r='5' />";
                //                    $output .= 'M10 10 H 90 V 90 H 10 L 10 10 ';
                //                }
                //                else{
                //                    // do other stuff, the module is light
                //                }
            }
        }

        //        foreach($matrix->matrix() as $y => $row){
        //            foreach($row as $x => $module){
        //
        //                // get a module's value
        //                $value = $module;
        //                $value = $matrix->get($x, $y);
        //
        //                // boolean check a module
        //                if($matrix->check($x, $y)){ // if($module >> 8 > 0)
        //                    // do stuff, the module is dark
        //                }
        //                else{
        //                    // do other stuff, the module is light
        //                }
        //
        //            }
        //        }
        //dd($output);
        return $output;
    }

    /**
     * @inheritDoc
     */
    protected function setModuleValues(): void
    {
        // TODO: Implement setModuleValues() method.
    }
}

//Route::get('/gg', 'HomeController@gradient')->name('gradient.index');
Route::get(
    'gg',
    function () {

        //$qrcode = 'https://www.youtube.com/watch?v=DLzxrzFCyOs&t=42s';
        //$data = 'http://lucidegreen.com';
        //$data = 'https://en.forums.wordpress.com/topic/save-draft-button-missing/aaaa';

        $data = 'https://prototype.lucidgreen.io/login?u=budtender@lucidgreen.io';
        //$data = 'https://prototype.lucidgreen.io/login?u=consumer@lucidgreen.io';
        $options = new QROptions(
            [
                'version'         => QRCode::VERSION_AUTO,//7
                'scale'           => 5,
                'outputType'      => QRCode::OUTPUT_CUSTOM,
                'outputInterface' => CO::class,
                //'outputType'      => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel'        => QRCode::ECC_M,
                'addQuietzone'    => true,
                'cssClass'        => 'my-css-class',
                'svgOpacity'      => 1.0,
                'svgDefs'         => '
<linearGradient id="g1" gradientTransform="skewX(0) translate(200,0) rotate(135)" gradientUnits="userSpaceOnUse">
    <stop offset="0" stop-color="#69b49d" />
    <stop offset="0.2" stop-color="#31779c" />
</linearGradient>
<style>rect{shape-rendering:crispEdges}</style>',
                'moduleValues'    => [
                    // finder
                    1536 => 'url(#g1)', // dark (true)
                    6    => '#fff', // light (false)
                    // alignment
                    2560 => 'url(#g1)',
                    10   => '#fff',
                    // timing
                    3072 => 'url(#g1)',
                    12   => '#fff',
                    // format
                    3584 => 'url(#g1)',
                    14   => '#fff',
                    // version
                    4096 => 'url(#g1)',
                    16   => '#fff',
                    // data
                    1024 => 'url(#g1)',
                    4    => '#fff',
                    // darkmodule
                    512  => 'url(#g1)',
                    // separator
                    8    => '#fff',
                    // quietzone
                    18   => '#fff',
                ],
            ]
        );
        //$qrcode  = new QRCode($options);

        //header('Content-type: image/svg+xml');
        /*        echo '<?xml version="1.0" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN""http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';*/

        $qrcode = (new QRCode($options))->render($data);
        header('Content-type: image/svg+xml');
        $gzip = true;
        if ($gzip === true) {
            header('Vary: Accept-Encoding');
            header('Content-Encoding: gzip');
            $qrcode = gzencode($qrcode, 9);
        }
        echo $qrcode;

        //echo $qrcode->render($data);

    }
);
Route::get(

    'g',
    function () {

        $data = 'https://www.youtube.com/watch?v=DLzxrzFCyOs&t=42s';

        $options = new QROptions(
            [
                'version'      => 3,
                'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel'     => QRCode::ECC_L,
                //            'outputType'      => QRCode::OUTPUT_CUSTOM,
                //            'outputInterface' => MyCustomOutput::class,
                'scale'        => 5,
                'addQuietzone' => true,
                'cssClass'     => 'my-css-class',
                'svgOpacity'   => 1.0,
                'svgDefs'      => '

		<linearGradient id="g1" gradientTransform="skewX(35) translate(-100,0) scale(2.4,1)" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#31779c" />
			<stop offset="1" stop-color="#69b49d" />
            <!--<stop offset="0" stop-color="red" />
			<stop offset="0.5" stop-color="blue" />-->
		</linearGradient>
		<style>

		rect{shape-rendering:crispEdges}
		</style>',
                'moduleValues' => [
                    // finder
                    1536 => 'url(#g1)', // dark (true)
                    6    => '#fff', // light (false)
                    // alignment
                    2560 => 'url(#g1)',
                    10   => '#fff',
                    // timing
                    3072 => 'url(#g1)',
                    12   => '#fff',
                    // format
                    3584 => 'url(#g1)',
                    14   => '#fff',
                    // version
                    4096 => 'url(#g1)',
                    16   => '#fff',
                    // data
                    1024 => 'url(#g1)',
                    4    => '#fff',
                    // darkmodule
                    512  => 'url(#g1)',
                    // separator
                    8    => '#fff',
                    // quietzone
                    18   => '#fff',
                ],
            ]
        );

        $qrcode = new QRCode($options);
        //        $matrix = $qrcode->getMatrix($data);
        //        dd($matrix);
        echo $qrcode->render($data);

        //return view('gradient', compact('options', 'data'));
    }
);
//Auth::routes();
Route::get('/', 'ProductsController@index')->name('products.index');
Route::get('/list', 'ProductsController@list')->name('products.list');
Route::post('/products', 'ProductsController@store')->name('products.store');
Route::get(
    '/a',
    function () {

        //        $apnsHost = 'gateway.sandbox.push.apple.com';
        //        $apnsCert = 'a.pem';
        //        $apnsPort = 2195;
        //        $token = '422c48cdcf266348791635f1471106282eeefeba5ea0b3f3089e6a8b1059d038';
        //
        //        $streamContext = stream_context_create();
        //        stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
        //        $apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
        //        $payload['aps'] = array('alert' => 'Oh hai!', 'badge' => 1, 'sound' => 'default');
        //        $output = json_encode($payload);
        //        $token = pack('H*', str_replace(' ', '', $token));
        //        $apnsMessage = chr(0) . chr(0) . chr(32) . $token . chr(0) . chr(strlen($output)) . $output;
        //        fwrite($apns, $apnsMessage);
        //        socket_close($apns);
        //        fclose($apns);

        //        $apns = Iphone::createStreamContext();
        //        $data = [
        //            '422c48cdcf266348791635f1471106282eeefeba5ea0b3f3089e6a8b1059d038',
        //            'a116e7fae5e089e86d6650d52a1ebe97b071d13aea697fec1151548cb90c6b5d',
        //            '43a210ba52d9fa571b97e1fa1b9fd7548a417f2f3b0a526d572832ab283b42b2'
        //        ];
        //        foreach ($data as $row) {
        //            try {
        //                $deviceToken = $row;//$row['Pnuser']['token'];
        //                //if(!empty($payload['aps'])) unset($payload['aps']);
        //                $payload['aps'] = array('alert' => 'Testing', 'badge' => 0, 'sound' => 'default', 'id' => (!empty($data['Article']['id']) ? $data['Article']['id'] : null));
        //                //$payload = json_encode($payload);
        //
        //                Iphone::sendAPNSmessage($apns, $deviceToken, json_encode($payload), $apns);
        //
        //                //echo "Push sent to " . $deviceToken . "<br />\n";
        //
        //            } catch (Exception $e) {
        //                echo "1. Exception: " . $e->getMessage() . "<br />\n";
        //            }
        //        }
        //
        //        Iphone::sendAPNSmessage($apns, $deviceToken, json_encode($payload), $apns);

        $deviceToken = '422c48cdcf266348791635f1471106282eeefeba5ea0b3f3089e6a8b1059d038';
        // 送信する文字列
        $alert = 'Push test.';
        // バッジ
        $badge                = 1;
        $body                 = array();
        $body['aps']          = array('alert' => $alert);
        $body['aps']['badge'] = $badge;
        // SSL証明書
        $cert = __DIR__ . '/a.pem';
        $url  = 'ssl://gateway.sandbox.push.apple.com:2195'; // 開発用
        //$url = 'ssl://gateway.push.apple.com:2195'; // 本番用
        $context = stream_context_create();
        stream_context_set_option($context, 'ssl', 'local_cert', $cert);
        $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $context);
        if (!$fp) {
            echo 'Failed to connect.' . PHP_EOL;
            exit(1);
        }
        $payload = json_encode($body);
        $message = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        print 'send message:' . $payload . PHP_EOL;
        fwrite($fp, $message);
        fclose($fp);

        $data = [
            (object)[
                'name'       => 'Ring [A0123]',
                'price'      => 25,
                'created_at' => '2016-07-20',
            ],
            (object)[
                'name'       => 'Pendant [A0124]',
                'price'      => 55,
                'created_at' => '2016-07-17',
            ],
            (object)[
                'name'       => 'Earrings [A0125]',
                'price'      => 15,
                'created_at' => '2016-06-20',
            ],
            (object)[
                'name'       => 'Bracelet [ABCDE]',
                'price'      => 15,
                'created_at' => '2016-06-23',
            ],
        ];
        //        $data = array_filter($data, function($item){
        //            return date("n",strtotime($item->created_at)) == 7;
        //        });
        dd(collect($data)->pluck('name')->all());
    }
);

Route::get(
    '/ds',
    function () {
        class DocuSignSample
        {
            public function signatureRequestFromTemplate()
            {
                $username       = "muhamed.didovic@gmail.com";
                $password       = "sanica000";
                $integrator_key = "c5462989-febe-44f7-b6b7-c8fcdbcb0baa";

                // change to production (www.docusign.net) before going live
                $host = "https://demo.docusign.net/restapi";

                // create configuration object and configure custom auth header
                $config = new DocuSign\eSign\Configuration();
                $config->setHost($host);
                $config->addDefaultHeader("X-DocuSign-Authentication", "{\"Username\":\"" . $username . "\",\"Password\":\"" . $password . "\",\"IntegratorKey\":\"" . $integrator_key . "\"}");

                // instantiate a new docusign api client
                $apiClient = new DocuSign\eSign\ApiClient($config);
                $accountId = null;

                try {
                    //*** STEP 1 - Login API: get first Account ID and baseURL
                    $authenticationApi = new DocuSign\eSign\Api\AuthenticationApi($apiClient);
                    $options           = new \DocuSign\eSign\Api\AuthenticationApi\LoginOptions();
                    $loginInformation  = $authenticationApi->login($options);
                    if (isset($loginInformation)) {
                        $loginAccount = $loginInformation->getLoginAccounts()[0];
                        $host         = $loginAccount->getBaseUrl();
                        $host         = explode("/v2", $host);
                        $host         = $host[0];

                        // UPDATE configuration object
                        $config->setHost($host);

                        // instantiate a NEW docusign api client (that has the correct baseUrl/host)
                        $apiClient = new DocuSign\eSign\ApiClient($config);

                        if (isset($loginInformation)) {
                            $accountId = $loginAccount->getAccountId();
                            if (!empty($accountId)) {
                                //*** STEP 2 - Signature Request from a Template
                                // create envelope call is available in the EnvelopesApi
                                $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($apiClient);
                                // assign recipient to template role by setting name, email, and role name.  Note that the
                                // template role name must match the placeholder role name saved in your account template.
                                $templateRole = new  DocuSign\eSign\Model\TemplateRole();
                                $templateRole->setEmail("muhamed.didovic@gmail.com");
                                $templateRole->setName("muha med");
                                $templateRole->setRoleName("buyers");

                                // instantiate a new envelope object and configure settings
                                $envelop_definition = new DocuSign\eSign\Model\EnvelopeDefinition();
                                $envelop_definition->setEmailSubject("[DocuSign PHP SDK] - Signature Request Sample");
                                $envelop_definition->setTemplateId("46e00d68-4ebf-4961-80c4-b93dafb74cfc");
                                $envelop_definition->setTemplateRoles(array($templateRole));

                                // set envelope status to "sent" to immediately send the signature request
                                $envelop_definition->setStatus("sent");

                                // optional envelope parameters
                                $options = new \DocuSign\eSign\Api\EnvelopesApi\CreateEnvelopeOptions();
                                $options->setCdseMode(null);
                                $options->setMergeRolesOnDraft(null);

                                // create and send the envelope (aka signature request)
                                $envelop_summary = $envelopeApi->createEnvelope($accountId, $envelop_definition, $options);
                                if (!empty($envelop_summary)) {
                                    echo "$envelop_summary";
                                }

                                $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($apiClient);

                                $return_url_request = new \DocuSign\eSign\Model\ReturnUrlRequest();
                                $return_url_request->setReturnUrl('http://ct.test/callback');
                                //                                echo '<pre>';
                                //                                print_r(json_decode($envelop_summary)->envelopeId);
                                //                                echo '</pre>';
                                //                                die();
                                //dd('envelop_definition', $envelopeApi);

                                //$senderView = $envelopeApi->createSenderView($accountId, json_decode($envelop_summary)->envelopeId, $return_url_request);

                                //                                $this->assertNotEmpty($senderView);
                                //                                $this->assertNotEmpty($senderView->getUrl());


                                $envelopeApi            = new DocuSign\eSign\Api\EnvelopesApi($apiClient);
                                $recipient_view_request = new \DocuSign\eSign\Model\RecipientViewRequest();
                                $recipient_view_request->setReturnUrl('http://ct.test/callback');
                                $recipient_view_request->setClientUserId('neki user');
                                $recipient_view_request->setAuthenticationMethod("email");
                                $recipient_view_request->setUserName('user name neki');
                                $recipient_view_request->setEmail('neki@mail.com');
                                $signingView = $envelopeApi->createRecipientView($accountId, json_decode($envelop_summary)->envelopeId, $recipient_view_request);

                            }
                        }
                    }
                } catch (Exception $ex) {
                    echo "Exception: " . $ex->getMessage() . "\n";
                }
            }
        }

        $u      = new DocuSignSample;
        $result = $u->signatureRequestFromTemplate();
        dd('sss', $result);
    }
);

//Route::get(
//    'b',
//    function () {
//
//        //
//        // DocuSign API Quickstart - Embedded Signing
//        //
//        // Download PHP client:  https://github.com/docusign/DocuSign-PHP-Client
//        //    require_once './DocuSign-PHP-Client/src/DocuSign_Client.php';
//        //    require_once './DocuSign-PHP-Client/src/service/DocuSign_RequestSignatureService.php';
//        //    require_once './DocuSign-PHP-Client/src/service/DocuSign_ViewsService.php';
//        //=======================================================================================================================
//        // STEP 1: Login API
//        //=======================================================================================================================
//        //    $username = "muhamed.didovic@gmail.com";
//        //    $password = "sanica000";
//        //    $integrator_key = "c5462989-febe-44f7-b6b7-c8fcdbcb0baa";
//        // client configuration
//        $testConfig = array(
//            // Enter your Integrator Key, Email, and Password
//            'integrator_key' => "c5462989-febe-44f7-b6b7-c8fcdbcb0baa",
//            'email'          => "muhamed.didovic@gmail.com",
//            'password'       => "sanica000",
//            // API version and environment (demo, www, etc)
//            'version'        => 'v2',
//            'environment'    => 'demo',
//        );
//        // instantiate client object and call Login API
//        $client = new DocuSign_Client($testConfig);
//        if ($client->hasError()) {
//            echo "\nError encountered in client, error is: " . $client->getErrorMessage() . "\n";
//
//            return;
//        }
//        //=======================================================================================================================
//        // STEP 2: Create and Send Envelope API (with embedded recipient)
//        //=======================================================================================================================
//        $service = new DocuSign_RequestSignatureService($client);
//        // Configure envelope settings, document(s), and recipient(s)
//        $emailSubject = "Please sign my document";
//        $emailBlurb   = "This goes in the email body";
//        // create one signHere tab for the recipient
//        $tabs       = array(
//            "signHereTabs" => array(
//                array("documentId" => "1", "pageNumber" => "1", "xPosition" => "100", "yPosition" => "150"),
//            ),
//        );
//        $recipients = array(new DocuSign_Recipient("1", "1", "RECIPIENT_NAME", "RECIPIENT_EMAIL", "101", 'signers', $tabs));
//        $documents  = array(new DocuSign_Document("TEST.PDF", "1", file_get_contents("TEST.PDF")));
//        // "sent" to send immediately, "created" to save as draft in your account
//        $status = 'sent';
//        //*** Send the signature request!
//        $response = $service->signature->createEnvelopeFromDocument(
//            $emailSubject,
//            $emailBlurb,
//            $status,
//            $documents,
//            $recipients,
//            array()
//        );
//        echo "\n-- Results --\n\n";
//        print_r($response);
//        //=======================================================================================================================
//        // STEP 3: Request Recipient View API (aka Signing URL)
//        //=======================================================================================================================
//        // Now get the recipient view
//        $service    = new DocuSign_ViewsService($client);
//        $returnUrl  = "http://www.docusign.com/developer-center";
//        $authMethod = "email";
//        $envelopeId = $response->envelopeId;
//        $response   = $service->views->getRecipientView(
//            $returnUrl,
//            $envelopeId,
//            "RECIPIENT_NAME",
//            "RECIPIENT_EMAIL",
//            "101",
//            $authMethod
//        );
//        echo "\nOpen the following URL to sign the document:\n\n";
//        print_r($response);
//    }
//);

Route::get(
    '/home',
    function () {
        // Input your info:

        $email         = "pavel.stepanov@gmail.com"; //"86717902-0f70-4bfe-9893-8a78a9407483"; //26950388
        $password      = "tymbl18";
        $integratorKey = "ff6cbf0b-3bff-4f72-95a0-3cc5edaa0b81";
        $templateId    = "195fc338-7395-416f-b20a-c25fe4d10074";//"c408c013-0178-48fd-96c1-9a53a201b0c2";//"c408c013-0178-48fd-96c1-9a53a201b0c2";//"ed2ceb75-78df-44ae-b185-8e7efdf61e9d";

        //        $email         = "muhamed.didovic@gmail.com";
        //        $password      = "sanica000";
        //        $integratorKey = "c5462989-febe-44f7-b6b7-c8fcdbcb0baa";
        //        $templateId    = "32db87a4-ca82-4156-8474-ca3812cb6068";//"8d06fd1d-c86b-4d1b-bd29-addb06bf2823";//"46e00d68-4ebf-4961-80c4-b93dafb74cfc";

        //$templateRoleName = "buyers";
        $clientUserId   = "59";
        $recipientName  = "md1 template role buyer";//"Timour as buyer";
        $recipientEmail = 'muhamed_didovic@hotmail.com';//"timourkh@gmail.com";

        // construct the authentication header:
        $header = "<DocuSignCredentials><Username>" . $email . "</Username><Password>" . $password . "</Password><IntegratorKey>" . $integratorKey . "</IntegratorKey></DocuSignCredentials>";

        /////////////////////////////////////////////////////////////////////////////////////////////////
        // STEP 1 - Login (retrieves baseUrl and accountId)
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $url = "https://demo.docusign.net/restapi/v2/login_information";
        //$url  = "https://docusign.com/restapi/v2/login_information/";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-DocuSign-Authentication: $header"));

        $json_response = curl_exec($curl);
        $status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($status != 200) {
            //echo "1error calling webservice, status is:" . $status;
            echo "1error calling webservice, status is:" . $status . "\nerror text is --> ";
            print_r($json_response);
            echo "\n";
            exit(-1);
        }

        $response  = json_decode($json_response, true);
        $accountId = $response["loginAccounts"][0]["accountId"];
        $baseUrl   = $response["loginAccounts"][0]["baseUrl"];
        curl_close($curl);

        //--- display results
        echo "accountId = $accountId \n baseUrl $baseUrl \n";
        //die('1');
        /////////////////////////////////////////////////////////////////////////////////////////////////
        // STEP 1.1 - Getting a Template
        /////////////////////////////////////////////////////////////////////////////////////////////////
        //        curl -i -H 'X-DocuSign-Authentication:
        //              { "Username": "developer@example.com",
        //                "Password": "example",
        //                "IntegratorKey":"00000000-aaaa-bbbb-cccc-0123456789b" }'\
        //        https://demo.docusign.net/restapi/v2/accounts/9999999/templates/00000000-aaaa-bbbb-cccc-0123456789c


        //        $curl        = curl_init($baseUrl . "/templates/" . $templateId);
        //        curl_setopt($curl, CURLOPT_HEADER, false);
        //        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-DocuSign-Authentication: $header"));
        //        $json_response = curl_exec($curl);
        //        $status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //        if ($status != 200) {
        //            echo "1.1error calling webservice, status is:" . $status . "\nerror text is --> ";
        //            print_r($json_response);
        //            echo "\n";
        //            exit(-1);
        //        }
        //
        //        $response  = json_decode($json_response, true);
        //dd('1.1:',$response);

        /////////////////////////////////////////////////////////////////////////////////////////////////
        // STEP 2 - Create an envelope with an Embedded recipient (uses the clientUserId property)
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $data = [
            "status"       => 'sent',
            "accountId"    => $accountId,
            "emailSubject" => "DocuSign API - Embedded Signing Example from CT Laravel",
            "templateId"   => $templateId,

            "templateRoles" => [
                [
                    "roleName"     => 'buyers',
                    //                    "name"     => 'Timour as buyer',
                    //                    "email"    => 'timourkh@gmail.com',
                    "email"        => "muhamed_didovic@hotmail.com",
                    "name"         => "md1 template role buyer ",
                    "clientUserId" => $clientUserId,

                    "signers" => [
                        [
                            "name"         => "md1 template role buyer ",
                            "email"        => "muhamed_didovic@hotmail.com",
                            "recipientId"  => $clientUserId,
                            "clientUserId" => $clientUserId,
                            "routingOrder" => "1",
                            "roleName"     => "buyers",
                            "tabs"         => [
                                "textTabs" => [
                                    //                                    [
                                    //                                        "xPosition" => "77",
                                    //                                        "yPosition" => "402",
                                    //                                        "name"      => "First Name",
                                    //                                        "tabLabel"  => "first",
                                    //                                        "value"     => "Cchirag",
                                    //                                    ],
                                    //                                    [
                                    //                                        "xPosition" => "156",
                                    //                                        "yPosition" => "402",
                                    //                                        "name"      => "Last Name",
                                    //                                        "tabLabel"  => "last",
                                    //                                        "value"     => "Netmaxims",
                                    //                                    ],
                                    //                                    [
                                    //                                        "xPosition" => "235",
                                    //                                        "yPosition" => "402",
                                    //                                        "name"      => "Title",
                                    //                                        "tabLabel"  => "title",
                                    //                                        "value"     => "Default title value",
                                    //                                    ],

                                    //                                    [
                                    //                                        //                                        "xPosition" => "235",
                                    //                                        //                                        "yPosition" => "402",
                                    //                                        "name"     => "text1",
                                    //                                        "tabLabel" => "text1",
                                    //                                        "value"    => "text1",
                                    //                                    ],

                                ],
                            ],
                        ],

                    ],
                ],
                [
                    "roleName" => 'sellers',
                    "name"     => 'md2 seller yahoo',
                    "email"    => 'muhamed.didovic@yahoo.com',
                    //"clientUserId" => $clientUserId,

                    "signers" => [
                        [
                            //                            "name"         => "Timour",//Timour Khamnayev <timourkh@gmail.com>
                            //                            "email"        => "timourkh@gmail.com",

                            //"name"  => "irmaa",//Timour Khamnayev <timourkh@gmail.com>
                            //"email" => "timourkh@gmail.com",
                            "recipientId"  => "200",
                            "clientUserId" => "200",//rand(1, 1000),
                            "routingOrder" => "2",
                            "roleName"     => "sellers",
                            //                            "tabs"         => [
                            //                                "textTabs" => [
                            //                                    [
                            //                                        "xPosition" => "77",
                            //                                        "yPosition" => "402",
                            //                                        "name"      => "First Name",
                            //                                        "tabLabel"  => "first",
                            //                                        "value"     => "Cchirag",
                            //                                    ],
                            //                                    [
                            //                                        "xPosition" => "156",
                            //                                        "yPosition" => "402",
                            //                                        "name"      => "Last Name",
                            //                                        "tabLabel"  => "last",
                            //                                        "value"     => "Netmaxims",
                            //                                    ],
                            //                                    [
                            //                                        "xPosition" => "235",
                            //                                        "yPosition" => "402",
                            //                                        "name"      => "Title",
                            //                                        "tabLabel"  => "title",
                            //                                        "value"     => "Default title value",
                            //                                    ],
                            //
                            //                                    [
                            //                                        //                                        "xPosition" => "235",
                            //                                        //                                        "yPosition" => "402",
                            //                                        "name"     => "text1",
                            //                                        "tabLabel" => "text1",
                            //                                        "value"    => "text1",
                            //                                    ],
                            //
                            //                                ],
                            //                            ],
                        ],

                    ],
                ],
            ],
        ];

        $data_string = json_encode($data);
        $curl        = curl_init($baseUrl . "/envelopes");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                "X-DocuSign-Authentication: $header",
            )
        );

        $json_response = curl_exec($curl);
        $status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 201) {
            echo "2error calling webservice, status is:" . $status . "\nerror text is --> ";
            print_r($json_response);
            echo "\n";
            exit(-1);
        }

        $response   = json_decode($json_response, true);
        $envelopeId = $response["envelopeId"];
        curl_close($curl);

        //--- display results
        echo "Envelope created! Envelope ID: " . $envelopeId . "\n";
        //die('da vidimo');
        /////////////////////////////////////////////////////////////////////////////////////////////////
        // STEP 3 - Get the Embedded Singing View
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $data = array(
            "returnUrl"            => "http://ct.test/callback",
            "authenticationMethod" => "None",
            "email"                => $recipientEmail,
            "userName"             => $recipientName,
            //"role"                 => "buyers",
            "clientUserId"         => $clientUserId,
        );

        $data_string = json_encode($data);
        $curl        = curl_init($baseUrl . "/envelopes/$envelopeId/views/recipient");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                "X-DocuSign-Authentication: $header",
            )
        );

        $json_response = curl_exec($curl);
        $status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 201) {
            echo "3error calling webservice, status is:" . $status . "\nerror text is --> ";
            print_r($json_response);
            echo "\n";
            exit(-1);
        }

        $response = json_decode($json_response, true);
        $url      = $response["url"];

        //--- display results
        echo "Embedded URL is: \n\n" . $url . "\n\nNavigate to this URL to start the embedded signing view of the envelope\n";
        header('Location: ' . $url);
    }
);


Route::get(
    'callback',
    function () {
        dd('callback get:', $_GET, 'POST:', $_POST);
    }
);

Route::post(
    'callback',
    function () {
        dd('callback post:', $_GET, 'POST:', $_POST);
    }
);

/*Route::get(
    'ecv',
    function () {
        // Input your info:
        //    $email            = "muhamed.didovic@gmail.com";
        //    $password         = "sanica000";
        //    $integratorKey    = "c5462989-febe-44f7-b6b7-c8fcdbcb0baa";
        //    $recipientName    = "memo23";
        //    $templateId       = "46e00d68-4ebf-4961-80c4-b93dafb74cfc";
        //    $templateRoleName = "signer1";
        //    $clientUserId     = "59";
        $email         = "muhamed.didovic@gmail.com";            // your account email
        $password      = "sanica000";        // your account password
        $integratorKey = "c5462989-febe-44f7-b6b7-c8fcdbcb0baa";;        // your account integrator key, found on (Preferences -> API page)
        // construct the authentication header:
        $header = "<DocuSignCredentials><Username>" . $email . "</Username><Password>" . $password . "</Password><IntegratorKey>" . $integratorKey . "</IntegratorKey></DocuSignCredentials>";
        /////////////////////////////////////////////////////////////////////////////////////////////////
        // STEP 1 - Login (retrieves baseUrl and accountId)
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $url  = "https://demo.docusign.net/restapi/v2/login_information";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-DocuSign-Authentication: $header"));
        $json_response = curl_exec($curl);
        $status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            echo "error calling webservice, status is:" . $status;
            exit(-1);
        }
        $response  = json_decode($json_response, true);
        $accountId = $response["loginAccounts"][0]["accountId"];
        $baseUrl   = $response["loginAccounts"][0]["baseUrl"];
        curl_close($curl);
        //--- display results
        echo "accountId = " . $accountId . "\nbaseUrl = " . $baseUrl . "\n";
        /////////////////////////////////////////////////////////////////////////////////////////////////
        // STEP 2 - Get the Embedded Console view
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $data        = array("accountId" => $accountId);
        $data_string = json_encode($data);
        $curl        = curl_init($baseUrl . "/views/console");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                "X-DocuSign-Authentication: $header",
            )
        );
        $json_response = curl_exec($curl);
        $status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 201) {
            echo "error calling webservice, status is:" . $status . "\nerror text is --> ";
            print_r($json_response);
            echo "\n";
            exit(-1);
        }
        $response = json_decode($json_response, true);
        $url      = $response["url"];
        //--- display results
        echo "Embedded URL is: \n\n" . $url . "\n\nNavigate to this URL to open the DocuSign Member Console...\n";

        header('Location: ' . $url);
    }
);*/

