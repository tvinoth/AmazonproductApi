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
*  @Date        01-08-2017
*  @Developerversion  version 1
*/
	//include mws credentials
	include_once ('.config.inc.php'); 
	if(isset($_REQUEST['sendsubmissionid']) && $_REQUEST['sendsubmissionid'] == "SubmissionResult")
	{
	    $feedid   = 	$_REQUEST['submissionid'];
	    if($feedid 	==	"")
	    {
	    	$result   	=	array(
      							'Result'=>0,
								'Message'=>'Submission id is required'
								);  
			echo json_encode([$result]);    
			return false;
	    }

	    $serviceUrl 	= 	"https://mws.amazonservices.in";
		$config 		= 	array (
							'ServiceURL' => $serviceUrl,
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

		$parameters = 	array 
						(
							'Merchant' => MERCHANT_ID,
							'FeedSubmissionId' => $feedid,
							'FeedSubmissionResult' => @fopen('php://memory', 'rw+'),
							'MWSAuthToken' => 'amzn.mws.c8aecbfd-882c-f4fe-5aff-a0cd3004e6b6', // Optional
						);
		$request 	= 	new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);
		$request 	= 	new MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
		$request->setMerchant(MERCHANT_ID);
		$request->setFeedSubmissionId($feedid);
		$request->setFeedSubmissionResult(@fopen('php://memory', 'rw+'));
		$request->setMWSAuthToken('amzn.mws.c8aecbfd-882c-f4fe-5aff-a0cd3004e6b6'); // Optional     
		invokeGetFeedSubmissionResult($service, $request);
	}
	else
	{
		$result   	=	array(
      							'Result'=>0,
								'Message'=>'Invalid Access'
								);   
		echo json_encode([$result]);	
	}
	
	function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request) 
  	{
      	try 
      	{
            $response 	=	$service->getFeedSubmissionResult($request);
            if($response->isSetGetFeedSubmissionResultResult()) 
            {
              	$getFeedSubmissionResultResult 	= 	$response->getGetFeedSubmissionResultResult();
              	if($getFeedSubmissionResultResult->isSetContentMd5()) 
              	{
                	$result[] 	=	array('FeedSubmitFeedResult'=>1,
                				'ContentMd5'=>$getFeedSubmissionResultResult->getContentMd5()
                			);
              	}
            }
            if($response->isSetResponseMetadata()) 
            { 
            	$result[] 	=	array(
                				'ResponseMetadata'=>$response->getResponseMetadata()
                			);
            	$responseMetadata 	= 	$response->getResponseMetadata();
                if($responseMetadata->isSetRequestId()) 
                {
                	$result[] 	=	array(
                				'RequestId'=>$responseMetadata->getRequestId()
                			);
                }
            } 
            $result[] 	=	array(
                				'ResponseHeaderMetadata'=>$response->getResponseHeaderMetadata()
                			);
            echo json_encode($result);    
     	} 	
     	catch(MarketplaceWebService_Exception $ex) 
     	{
			$result   	=	array(
  							'FeedSubmitFeedResult'=>0,
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