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
	// $serviceUrl = "https://mws.amazonservices.com.cn/FulfillmentInventory/2010-10-01";
	$serviceUrl = "https://mws.amazonservices.in/FulfillmentInventory/2010-10-01";
	$config 	= 	array (
							'ServiceURL' => $serviceUrl,
							'ProxyHost' => null,
							'ProxyPort' => -1,
							'ProxyUsername' => null,
							'ProxyPassword' => null,
							'MaxErrorRetry' => 3,
						);

	$service 	= 	new FBAInventoryServiceMWS_Client(
										AWS_ACCESS_KEY_ID,
										AWS_SECRET_ACCESS_KEY,
										$config,
										APPLICATION_NAME,
										APPLICATION_VERSION
										);
	$request 	= 	new FBAInventoryServiceMWS_Model_GetServiceStatusRequest();
 	$request->setSellerId(MERCHANT_ID);
 	// object or array of parameters
 	invokeGetServiceStatus($service, $request);

 	function invokeGetServiceStatus(FBAInventoryServiceMWS_Interface $service, $request)
	{
	  	try 
	  	{
		    $response 	= 	$service->GetServiceStatus($request);
		   	$dom 		= 	new DOMDocument();
		    $dom->loadXML($response->toXML());
		    $dom->preserveWhiteSpace = false;
		    $dom->formatOutput = true;
		    $result[] 	=	array('Servicestatus'=>1,
		    	'ServiceResult'=>$dom->saveXML());
		    $result[] 	=	array('ResponseHeaderMetadata'=>$response->getResponseHeaderMetadata());
		    echo json_encode($result);
		} 
		catch (FBAInventoryServiceMWS_Exception $ex) 
		{
		    $result   	=	array(
      							'Servicestatus'=>0,
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