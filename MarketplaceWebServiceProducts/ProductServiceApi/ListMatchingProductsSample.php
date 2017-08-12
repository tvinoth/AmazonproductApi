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
*  @Date        04-08-2017
*  @Developerversion  version 1
*/
	require_once('.config.inc.php');
	$config 	= 	array (
						'ServiceURL' => MWSSERVICEURL,
						'ProxyHost' => null,
						'ProxyPort' => -1,
						'ProxyUsername' => null,
						'ProxyPassword' => null,
						'MaxErrorRetry' => 3,
					);
	$service 	= 	new MarketplaceWebServiceProducts_Client(
						AWS_ACCESS_KEY_ID,
						AWS_SECRET_ACCESS_KEY,
						APPLICATION_NAME,
						APPLICATION_VERSION,
						$config);
	$request 	= 	new MarketplaceWebServiceProducts_Model_ListMatchingProductsRequest();
 	$request->setSellerId(MERCHANT_ID);
 	$request->setQuery("iphone 6s mobile phones");
 	$request->setMarketplaceId(MARKETPLACE_ID1);
 	invokeListMatchingProducts($service, $request);
 	function invokeListMatchingProducts(MarketplaceWebServiceProducts_Interface $service, $request)
	{
		try 
		{
			$response 	= 	$service->ListMatchingProducts($request);
			$dom 		= 	new DOMDocument();
			$dom->loadXML($response->toXML());
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$xml 		= 	simplexml_load_string($dom->saveXML());
			echo 	$dom->saveXML();
			echo 'ResponseHeaderMetadata : '.$response->getResponseHeaderMetadata()."\n";
			// $result[] 	=	array('Listingproduct'=>$xml);
			// $result[] 	=	array('ResponseHeaderMetadata'=>$response->getResponseHeaderMetadata());
			// echo json_encode($result); 
		} 
		catch (MarketplaceWebServiceProducts_Exception $ex) 
		{
			$result   	=	array(
      							'ListMatchingproductresult'=>0,
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