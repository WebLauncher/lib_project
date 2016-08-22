<?php
class SiteLinkReportingApi
{
    private $_wsdl = 'https://api.smdservers.net/CCWs_3.5/ReportingWs.asmx?WSDL';
    private $_corp_code = 'CCTST';
    private $_loc_code = '';
    private $_corp_login = 'Administrator';
    private $_corp_pass = 'Demo';

    private $_client = null;
    
    private $dateMethods=array(
        'ManagementSummary',
        'RentRoll',
        'RentalActivity',
        'IncomeAnalysis',
        'BadDebts',
        'InsuranceActivity',
        'InsuranceRoll',
        'InsuranceActivityForAPI',
        'InsuranceStatement',
        'InsuranceSummary',
        'ConsolidatedManagementSummary',
        'MerchandiseSummary',
        'MerchandiseActivity',
        'AccountsReceivable',
        'MarketingSummary',
        'ManagementHistory',
        'MoveInsAndMoveOuts',
        'InquiryTracking',
        'Discounts',
        'GeneralJournalEntries',
        'FinancialSummary',
        'DailyDeposits',
        'Receipts',
        'PastDueBalances',
        'ChargesAndPaymentsComplete'
    );

    /**
     * Constructor
     */
    function __construct($corp_code = '', $loc_code = '', $corp_login = '', $corp_pass = '')
    {
        $this->_corp_code = $corp_code ? $corp_code : $this->_corp_code;
        $this->_loc_code = $loc_code ? $loc_code : $this->_loc_code;
        $this->_corp_login = $corp_login ? $corp_login : $this->_corp_login;
        $this->_corp_pass = $corp_pass ? $corp_pass : $this->_corp_pass;

        $this->_client = new SoapClient($this->_wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));
    }

    function get_location_code()
    {
        return $this->_loc_code;
    }
    
    function __call($method,$params){
        if(in_array($method,$this->dateMethods))
        {
            $pars = new stdClass();
            $pars->dReportDateStart = $params[0];
            $pars->dReportDateEnd = $params[1];
            return $this->_call($method, $pars);
        }
        return null;
    }

    /**
     * Private: call
     */
    private function _call($method, $params)
    {
        $params->sCorpCode = $this->_corp_code;
        $params->sLocationCode = $this->_loc_code;
        $params->sCorpUserName = $this->_corp_login;
        $params->sCorpPassword = $this->_corp_pass;
        $response = null;
        try {
            $response = $this->_client->{$method}($params);
        } catch(Exception $ex) {
            trigger_error('SiteLink API Error: ' . $ex->getMessage() . '<br>' . $ex);
        }
        return $this->_process_response($response);
    }

    private function _process_response($response)
    {
        return $response;
    }

    /**
     * TenantNew is used to create a new basic tenant with only a first name
     * and a last name for means of
     * creating a new reservation.
     */
    public function ScheduledAuctions()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }
}
?>