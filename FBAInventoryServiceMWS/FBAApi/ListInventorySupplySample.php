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
	// $serviceUrl 	= 	"https://mws.amazonservices.com.cn/FulfillmentInventory/2010-10-01";
$serviceUrl 	= 	"https://mws.amazonservices.in/FulfillmentInventory/2010-10-01";
	include_once ('.config.inc.php'); 
	$config 		= 	array (
							'ServiceURL' => $serviceUrl,
							'ProxyHost' => null,
							'ProxyPort' => -1,
							'ProxyUsername' => null,
							'ProxyPassword' => null,
							'MaxErrorRetry' => 3,
							);

	$service 		= 	new FBAInventoryServiceMWS_Client(
				                   AWS_ACCESS_KEY_ID,
				                   AWS_SECRET_ACCESS_KEY,
				                   $config,
				                   APPLICATION_NAME,
				                   APPLICATION_VERSION);

	$skus 	= 	new FBAInventoryServiceMWS_Model_SellerSkuList();
	$skus->setmember("V0-CTT8-IFES");
	// $skus->setmember("25075-9x12");
	$request 		= 	new FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
 	$request->setSellerId(MERCHANT_ID);
 	$request->setSellerSkus($skus);
	// $request->setSellerSkus('V0-CTT8-IFES'); 
 	// object or array of parameters
 	invokeListInventorySupply($service, $request);
 	function invokeListInventorySupply(FBAInventoryServiceMWS_Interface $service, $request)
	{
	 	try 
	 	{
		    $response 	= 	$service->ListInventorySupply($request);
		    $dom 		= 	new DOMDocument();
		    $dom->loadXML($response->toXML());
		    $dom->preserveWhiteSpace = false;
		    $dom->formatOutput = true;
		    echo $dom->saveXML();
		    echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
		} 
		catch (FBAInventoryServiceMWS_Exception $ex) 
		{
	    	$result   	=	array(
      							'ListInventoryResult'=>0,
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