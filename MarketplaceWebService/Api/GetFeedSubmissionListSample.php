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
						'FeedProcessingStatusList' => array ('Status' => array ('_SUBMITTED_','_DONE_')),
						'MWSAuthToken' => MWSAUTHORISATIONTOKEN
						);

	$request 	= 	new MarketplaceWebService_Model_GetFeedSubmissionListRequest($parameters);
	// $request 	= 	new MarketplaceWebService_Model_GetFeedSubmissionListRequest();
	$request->setMerchant(MERCHANT_ID);
	$request->setMWSAuthToken(MWSAUTHORISATIONTOKEN);
	
    // $statusList     =   new MarketplaceWebService_Model_StatusList();
    // $request->setFeedProcessingStatusList($statusList->withStatus('_SUBMITTED_','_DONE_'));
	invokeGetFeedSubmissionList($service, $request);
	
	function invokeGetFeedSubmissionList(MarketplaceWebService_Interface $service, $request) 
  	{
      	try 
      	{
            $response 	= 	$service->getFeedSubmissionList($request);
            if ($response->isSetGetFeedSubmissionListResult()) 
            {                     
                $getFeedSubmissionListResult 	= 	$response->getGetFeedSubmissionListResult();
                if ($getFeedSubmissionListResult->isSetNextToken()) 
                {
                	$result[] 	=	array('GetFeedSubmissionListResult'=>1,
            						'NextToken'=>$getFeedSubmissionListResult->getNextToken()
            						);
                }
                if ($getFeedSubmissionListResult->isSetHasNext()) 
                {
                	$result[] 	=	array(
            						'HasNext'=>$getFeedSubmissionListResult->getHasNext()
            						);
                }
                $feedSubmissionInfoList = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
                foreach ($feedSubmissionInfoList as $feedSubmissionInfo) 
                {
                    if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                    {
                    	$result[] 	=	array(
        						'FeedSubmissionId'=>$feedSubmissionInfo->getFeedSubmissionId()
        						);
                    }
                    if ($feedSubmissionInfo->isSetFeedType()) 
                    {
                    	$result[] 	=	array(
        						'FeedType'=>$feedSubmissionInfo->getFeedType()
        						);
                    }
                    if ($feedSubmissionInfo->isSetSubmittedDate()) 
                    {
                    	$result[] 	=	array(
    						'SubmittedDate'=>$feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT)
    						);
                    }
                    if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                    {
                    	$result[] 	=	array(
    						'FeedProcessingStatus'=>$feedSubmissionInfo->getFeedProcessingStatus()
    						);
                    }
                    if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                    {
                    	$result[] 	=	array(
    						'StartedProcessingDate'=>$feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT)
    						);
                    }
                    if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                    {
                    	$result[] 	=	array(
    						'CompletedProcessingDate'=>$feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT)
    						);
                    }
                }
            } 
            if ($response->isSetResponseMetadata()) 
            { 
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) 
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