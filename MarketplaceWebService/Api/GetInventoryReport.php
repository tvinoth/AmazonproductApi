<?php
/** 
*  PHP Version 5
*
*  @package     MarketplaceWebService
*  @copyright   Copyright Falconnect Technologies.
*  @link        http://www.falconnect.in/
*  @license     http://www.falconnect.in/
*  @version     2009-01-01
*  @Developer   vinoth 
*  @Date        11-08-2017
*  @Developerversion  version 1
*/
	//include mws credentials
	include_once ('.config.inc.php'); 
	$config 	= 	array (
							'ServiceURL' => MWSSERVICEURL,
							'ProxyHost' => null,
							'ProxyPort' => -1,
							'MaxErrorRetry' => 3,
							);
	$service 	= 	new MarketplaceWebService_Client(
											AWS_ACCESS_KEY_ID, 
											AWS_SECRET_ACCESS_KEY, 
											$config,
											APPLICATION_NAME,
											APPLICATION_VERSION
											);
	$reportId 	= 	'B01MCR4YC5';
 
	$parameters = 	array (
						'Merchant' => MERCHANT_ID,
						'Report' => @fopen('php://memory', 'rw+'),
						'ReportId' => $reportId,
						'MWSAuthToken' => MWSAUTHORISATIONTOKEN,
						);
	$request	= 	new MarketplaceWebService_Model_GetReportRequest($parameters);

// $request = new MarketplaceWebService_Model_GetReportRequest();
	$request->setMerchant(MERCHANT_ID);
	$request->setReport(@fopen('php://memory', 'rw+'));
	$request->setReportId($reportId);
	$request->setMWSAuthToken(MWSAUTHORISATIONTOKEN);
	invokeGetReport($service, $request);

	function invokeGetReport(MarketplaceWebService_Interface $service, $request) 
  	{
      	try 
      	{
            $response 	= 	$service->getReport($request);
                echo("        GetReportResponse\n");
            if ($response->isSetGetReportResult()) 
            {
                $getReportResult 	= 	$response->getGetReportResult(); 
					echo ("            GetReport");
				if ($getReportResult->isSetContentMd5()) 
				{
					echo ("                ContentMd5");
					echo ("                " . $getReportResult->getContentMd5() . "\n");
				}
            }
			
			if ($response->isSetResponseMetadata()) 
			{ 
			    echo("            ResponseMetadata\n");
			    $responseMetadata = $response->getResponseMetadata();
			    if ($responseMetadata->isSetRequestId()) 
			    {
			        echo("                RequestId\n");
			        echo("                    " . $responseMetadata->getRequestId() . "\n");
			    }
			}
                
			echo ("        Report Contents\n");
			echo (stream_get_contents($request->getReport()) . "\n");

			echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     	} 
     	catch (MarketplaceWebService_Exception $ex) 
     	{
         	$result   	=	array(
      							'GetFeedSubmissionListResult'=>0,
								'Caught_Exception'=>$ex->getMessage(),
								'Response_Status_Code'=>$ex->getStatusCode(),
								'Error_Code'=>$ex->getErrorCode(),
								'Error_Type'=>$ex->getErrorType(),
								'Request_ID'=>$ex->getRequestId(),
								'XML'=>$ex->getXML(),
								'ResponseHeaderMetadata'=>$ex->getResponseHeaderMetadata()
								);   
			echo json_encode([$result]); 
     	}
 	}
?>