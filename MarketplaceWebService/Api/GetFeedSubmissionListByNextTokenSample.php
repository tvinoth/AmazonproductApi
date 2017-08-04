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
	if(isset($_POST['submissionnexttoken']) && $_SERVER['REQUEST_METHOD'] 	==	"POST")
	{
	    $nextToken   = 	$_POST['submissionnexttoken'];
	    if($nextToken 	==	"")
	    {
	    	$result   	=	array(
      							'Result'=>0,
								'Message'=>'Submission Next Token is required'
								);  
			echo json_encode([$result]);    
			return false;
	    }

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
		 
		$parameters = 	array (
							'Merchant' => MERCHANT_ID,
							'NextToken' => $nextToken,
							'MWSAuthToken' => MWSAUTHORISATIONTOKEN,
						);
		$request 	= 	new MarketplaceWebService_Model_GetFeedSubmissionListByNextTokenRequest($parameters);
		$request->setMerchant(MERCHANT_ID);
		$request->setNextToken($nextToken);
		$request->setMWSAuthToken(MWSAUTHORISATIONTOKEN);

		invokeGetFeedSubmissionListByNextToken($service, $request);
	}
	else
	{
		$result   	=	array(
	  							'Result'=>0,
								'Message'=>'Invalid Access'
								);   
		echo json_encode([$result]);
	}

	function invokeGetFeedSubmissionListByNextToken(MarketplaceWebService_Interface 
													$service, $request) 
  	{
      	try 
      	{
            $response 	= 	$service->getFeedSubmissionListByNextToken($request);
            $result[]	=   array('Result'=>1);
			if ($response->isSetGetFeedSubmissionListByNextTokenResult()) 
			{ 
                $getFeedSubmissionListByNextTokenResult = $response->getGetFeedSubmissionListByNextTokenResult();
                if ($getFeedSubmissionListByNextTokenResult->isSetNextToken()) 
                {
                	$result[]	=   array('NextToken'=>$getFeedSubmissionListByNextTokenResult->getNextToken());
                }
                if ($getFeedSubmissionListByNextTokenResult->isSetHasNext()) 
                {
                	$result[]	=   array('HasNext'=>$getFeedSubmissionListByNextTokenResult->getHasNext());
                }
               	$feedSubmissionInfoList = $getFeedSubmissionListByNextTokenResult->getFeedSubmissionInfoList();
                foreach ($feedSubmissionInfoList as $feedSubmissionInfo) 
                {
                    if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                    {
                    	$result[]	=   array('FeedSubmissionInfo'=>1,
                    		'FeedSubmissionId'=>$feedSubmissionInfo->getFeedSubmissionId());
                    }
                    if ($feedSubmissionInfo->isSetFeedType()) 
                    {
                    	$result[]	=   array('FeedType'=>$feedSubmissionInfo->getFeedType());
                    }
                    if ($feedSubmissionInfo->isSetSubmittedDate()) 
                    {
                    	$result[]	=   array('SubmittedDate'=>$feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT));
                    }
                    if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                    {
                    	$result[]	=   array('FeedProcessingStatus'=>$feedSubmissionInfo->getFeedProcessingStatus());
                    }
                    if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                    {
                    	$result[]	=   array('StartedProcessingDate'=>$feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT));
                    }
                    if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                    {
                    	$result[]	=   array('CompletedProcessingDate'=>$feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT));
                    }
                }
            } 
            if ($response->isSetResponseMetadata()) 
            { 
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) 
                {
                	$result[]	=   array('RequestId'=>$responseMetadata->getRequestId());
                }
            } 
            $result[]	=   array('ResponseHeaderMetadata'=>$response->getResponseHeaderMetadata());
            echo json_encode($result);
    	} 

	    catch (MarketplaceWebService_Exception $ex) 
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