<?php
/** 
*  PHP Version 5
*
*  @category    Amazon
*  @package     MarketplaceWebService
*  @copyright   Copyright 2009 Amazon Technologies, Inc.
*  @link        http://aws.amazon.com
*  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
*  @version     2009-01-01
*  @Developer   vinoth 
*  @Date        01-08-2017
*  @Developerversion  version 1
*/
	//include mws credentials
	include_once ('.config.inc.php');
	$config 	= 	array 
						(
							'ServiceURL' => MWSSERVICEURL,
							'ProxyHost' => null,
							'ProxyPort' => -1,
							'MaxErrorRetry' => 3,
						);
	$service 	= 	new MarketplaceWebService_Client
						(
							AWS_ACCESS_KEY_ID, 
							AWS_SECRET_ACCESS_KEY, 
							$config,
							APPLICATION_NAME,
							APPLICATION_VERSION
						);

	$request 	= 	new MarketplaceWebService_Model_GetFeedSubmissionCountRequest();
	$request->setMerchant(MERCHANT_ID);
	// object or array of parameters
	invokeGetFeedSubmissionCount($service, $request);

	function invokeGetFeedSubmissionCount(MarketplaceWebService_Interface $service, $request)  
	{
		try 
		{
            $response 	= 	$service->getFeedSubmissionCount($request);
                if($response->isSetGetFeedSubmissionCountResult()) 
                { 
                	$result[]			=   array(
                									'Result'=>1
                								);
                    $getFeedSubmissionCountResult 	= 	$response->getGetFeedSubmissionCountResult();
                    if ($getFeedSubmissionCountResult->isSetCount()) 
                    {                        
                        $result[]   =   array('Count'=>$getFeedSubmissionCountResult->getCount());
                    }
                } 
                if($response->isSetResponseMetadata()) 
                {                     
                    $result[]   	=   array('ResponseMetadata'=>$response->getResponseMetadata());
                    $responseMetadata 	= 	$response->getResponseMetadata();
                    if($responseMetadata->isSetRequestId()) 
                    {
                      	$result[]   =   array('RequestId'=>$responseMetadata->getRequestId());
                    }
                } 
                $result[]			=   array(
                						'ResponseHeaderMetadata'=>$response->getResponseHeaderMetadata()
                						);
            echo json_encode($result);
     	} 
     	catch(MarketplaceWebService_Exception $ex) 
     	{
      		$result   	=	array(
      							'Result'=>0,
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