<?php

class SiteLinkApi
{
    private $_wsdl = 'https://api.smdservers.net/CCWs_3.5/CallCenterWs.asmx?WSDL';
    private $_corp_code = 'CCTST';
    private $_loc_code = '';
    private $_corp_login = 'Administrator';
    private $_corp_pass = 'Demo';
    private $_client = null;
    private $_last_call_start = 0;
    private $_last_call_end = 0;
    public $after_call_execute = '';
    public $cache = false;
    public $cache_pool = null;
    public $cache_ttl = 600;

    /**
     * Constructor
     */
    function __construct($corp_code = '', $loc_code = '', $corp_login = '', $corp_pass = '')
    {
        $this->_corp_code = $corp_code ? $corp_code : $this->_corp_code;
        $this->_loc_code = $loc_code ? $loc_code : $this->_loc_code;
        $this->_corp_login = $corp_login ? $corp_login : $this->_corp_login;
        $this->_corp_pass = $corp_pass ? $corp_pass : $this->_corp_pass;
        $this->_client = new SoapClient($this->_wsdl, array('cache_wsdl' => WSDL_CACHE_BOTH));
    }

    function get_location_code()
    {
        return $this->_loc_code;
    }

    /**
     * Private: call
     */
    private function _call($method, $params)
    {
        $response = null;
        $item = null;
        // check cache
        if ($this->cache && $this->cache_pool) {
            $item = $this->cache_pool->getItem($method, json_encode($params));
            $response = $item->get();
            if (!$item->isMiss()) {
                return $this->_process_response($response);
            }
        }

        $params->sCorpCode = $this->_corp_code;
        $params->sLocationCode = $this->_loc_code;
        $params->sCorpUserName = $this->_corp_login;
        $params->sCorpPassword = $this->_corp_pass;

        try {
            $this->_last_call_start = microtime(true);
            $response = $this->_client->{$method}($params);
            $this->_last_call_end = microtime(true);
            if ($this->after_call_execute && is_callable($this->after_call_execute))
                call_user_func($this->after_call_execute, $method);
        } catch (Exception $ex) {
            trigger_error('SiteLink API Error: ' . $ex->getMessage() . '<br>' . $ex);
        }

        if ($this->cache && $this->cache_pool) {
            $item->set($response, $this->cache_ttl);
        }

        return $this->_process_response($response);
    }

    public function get_duration()
    {
        return $this->_last_call_end - $this->_last_call_start;
    }

    private function _process_response($response)
    {
        return $response;
    }

    /**
     * 7.1 ACHProcessorSiteCurrentType
     * ACHProcessorSiteCurrentType returns the specified sites
     * current ACH processor name.
     */
    public function ACHProcessorSiteCurrentType()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.2 BulletinBoardInsert
     * BulletinBoardInsert is used to create a custom message for a site. This message
     * will be posted to the
     * specific site’s bulletin board in SiteLink.
     */
    public function BulletinBoardInsert($sSubject, $sBody)
    {
        $params = new stdClass();
        $params->sSubject = $sSubject;
        $params->sBody = $sBody;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.3 CCProcessorSiteCurrentType
     * CCProcessorSiteCurrentType returns the specified sites
     * current credit card processor name.
     */
    public function CCProcessorSiteCurrentType()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.4 ChargeAddToLedger
     * ChargeAddToLedger is used to add a specified charge to a ledger.
     * If a custom charge amount is not entered then the default charge
     * amount will be used. You can use ChargeDescriptionsRetrieve in
     * order to get the ChargeDescID for the charge that you want to add.
     *  Note that the custom charge price is the pre-tax price.
     */
    public function ChargeAddToLedger($LedgerID, $ChargeDescID, $dcAmtPreTax)
    {
        $params = new stdClass();
        $params->LedgerID = $LedgerID;
        $params->ChargeDescID = $ChargeDescID;
        $params->dcAmtPreTax = $dcAmtPreTax;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.5 ChargeDescriptionsRetrieve
     * ChargeDescriptionsRetrieve is used to get a list of all charge
     * description IDs for a specified site.
     */
    public function ChargeDescriptionsRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.6 ChargesAndPaymentsByLedgerID
     * ChargesAndPaymentsByLedgerID is used to get a list of all charges
     * and payments for a given ledger ID.
     */
    public function ChargesAndPaymentsByLedgerID($sLedgerID)
    {
        $params = new stdClass();
        $params->sLedgerID = $sLedgerID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.7 CompetitorAdd
     * CompetitorAdd is used to add a new competitor record.
     */
    public function CompetitorAdd($sName, $sAddress1, $sAddress2, $sCity, $sRegion, $sPostalCode, $sCountry, $sPhone, $sWebSiteURL, $dcLongitude, $dcLatitude, $dcDistanceMiles, $dcRateScaleFactor, $iTotalUnits, $iTotalArea, $dcAdminFee, $bResidentManager, $bFence, $bClimateControl, $bMerchandise, $bInsuranceOffered, $bInsuranceRequired, $bAdminFeeRequired, $bSecDepRequired, $sComment)
    {
        $params = new stdClass();
        $params->sName = $sName;
        $params->sAddress1 = $sAddress1;
        $params->sAddress2 = $sAddress2;
        $params->sCity = $sCity;
        $params->sRegion = $sRegion;
        $params->sPostalCode = $sPostalCode;
        $params->sCountry = $sCountry;
        $params->sPhone = $sPhone;
        $params->sWebSiteURL = $sWebSiteURL;
        $params->dcLongitude = $dcLongitude;
        $params->dcLatitude = $dcLatitude;
        $params->dcDistanceMiles = $dcDistanceMiles;
        $params->dcRateScaleFactor = $dcRateScaleFactor;
        $params->iTotalUnits = $iTotalUnits;
        $params->iTotalArea = $iTotalArea;
        $params->dcAdminFee = $dcAdminFee;
        $params->bResidentManager = $bResidentManager;
        $params->bFence = $bFence;
        $params->bClimateControl = $bClimateControl;
        $params->bMerchandise = $bMerchandise;
        $params->bInsuranceOffered = $bInsuranceOffered;
        $params->bInsuranceRequired = $bInsuranceRequired;
        $params->bAdminFeeRequired = $bAdminFeeRequired;
        $params->bSecDepRequired = $bSecDepRequired;
        $params->sComment = $sComment;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.8 CompetitorTrackingAdd
     * CompetitorTrackingAdd is used to add a new competitor rate record.
     */
    public function CompetitorTrackingAdd($iCompetitorID, $dcArea, $dcRate, $iFloor, $bPower, $bRVParking, $bClimate, $iCustAccess, $sComment)
    {
        $params = new stdClass();
        $params->iCompetitorID = $iCompetitorID;
        $params->dcArea = $dcArea;
        $params->dcRate = $dcRate;
        $params->iFloor = $iFloor;
        $params->bPower = $bPower;
        $params->bRVParking = $bRVParking;
        $params->bClimate = $bClimate;
        $params->iCustAccess = $iCustAccess;
        $params->sComment = $sComment;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.9 CompetitorTrackingList
     * CompetitorTrackingList is used to get a full list of competitor information
     *  stored in SiteLink. Each row will be a competitor rate and include the
     * information for the associated competitor.
     */
    public function CompetitorTrackingList()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.10 CompetitorTrackingUpdate
     * CompetitorTrackingUpdate is used to update an existing competitor rate record.
     */
    public function CompetitorTrackingUpdate($iCompetitorRateID, $dcArea, $dcRate, $iFloor, $bPower, $bRVParking, $bClimate, $iCustAccess, $sComment)
    {
        $params = new stdClass();
        $params->iCompetitorRateID = $iCompetitorRateID;
        $params->dcArea = $dcArea;
        $params->dcRate = $dcRate;
        $params->iFloor = $iFloor;
        $params->bPower = $bPower;
        $params->bRVParking = $bRVParking;
        $params->bClimate = $bClimate;
        $params->iCustAccess = $iCustAccess;
        $params->sComment = $sComment;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.11 CompetitorUpdate
     * CompetitorUpdate is used to update an existing competitor record.
     */
    public function CompetitorUpdate($CompetitorID, $sName, $sAddress1, $sAddress2, $sCity, $sRegion, $sPostalCode, $sCountry, $sPhone, $sWebSiteURL, $dcLongitude, $dcLatitude, $dcDistanceMiles, $dcRateScaleFactor, $iTotalUnits, $iTotalArea, $dcAdminFee, $bResidentManager, $bFence, $bClimateControl, $bMerchandise, $bInsuranceOffered, $bInsuranceRequired, $bAdminFeeRequired, $bSecDepRequired, $sComment)
    {
        $params = new stdClass();
        $params->CompetitorID = $CompetitorID;
        $params->sName = $sName;
        $params->sAddress1 = $sAddress1;
        $params->sAddress2 = $sAddress2;
        $params->sCity = $sCity;
        $params->sRegion = $sRegion;
        $params->sPostalCode = $sPostalCode;
        $params->sCountry = $sCountry;
        $params->sPhone = $sPhone;
        $params->sWebSiteURL = $sWebSiteURL;
        $params->dcLongitude = $dcLongitude;
        $params->dcLatitude = $dcLatitude;
        $params->dcDistanceMiles = $dcDistanceMiles;
        $params->dcRateScaleFactor = $dcRateScaleFactor;
        $params->iTotalUnits = $iTotalUnits;
        $params->iTotalArea = $iTotalArea;
        $params->dcAdminFee = $dcAdminFee;
        $params->bResidentManager = $bResidentManager;
        $params->bFence = $bFence;
        $params->bClimateControl = $bClimateControl;
        $params->bMerchandise = $bMerchandise;
        $params->bInsuranceOffered = $bInsuranceOffered;
        $params->bInsuranceRequired = $bInsuranceRequired;
        $params->bAdminFeeRequired = $bAdminFeeRequired;
        $params->bSecDepRequired = $bSecDepRequired;
        $params->sComment = $sComment;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.12 ConvenienceFeeAdd
     * ConvenienceFeeAdd is used to add the convenience fee charge to the specified
     * ledger. This method
     * also allows a custom convenience fee amount to be set. To use the default
     * convenience fee for the site
     * use -999 for the 6th parameter.
     * Note: The current recommended way to use convenience fees is as follows: add
     * the convenience
     * fee, check the balance, take the payment, if there is an error with the payment
     * then remove the
     * convenience fee.
     */
    public function ConvenienceFeeAdd($LedgerID, $dcAmtPreTax)
    {
        $params = new stdClass();
        $params->LedgerID = $LedgerID;
        $params->dcAmtPreTax = $dcAmtPreTax;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.13 ConvenienceFeeRemove
     * ConvenienceFeeRemove is used to remove the most recent convenience fee charge.
     * It will not allow you
     * to remove it if a Daily Close has been performed in the SiteLink client or if a
     * payment has been made on
     * the charge.
     * Note: The current recommended way to use convenience fees is as follows: add
     * the convenience
     * fee, check the balance, take the payment, if there is an error with the payment
     * then remove the
     * convenience fee.
     */
    public function ConvenienceFeeRemove($LedgerID)
    {
        $params = new stdClass();
        $params->LedgerID = $LedgerID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.14 ConvenienceFeeRetrieve
     * ConvenienceFeeRetrieve is used to get the value of the convenience fee charge
     * for a specified site.
     * Note: The current recommended way to use convenience fees is as follows: add
     * the convenience
     * fee, check the balance, take the payment, if there is an error with the payment
     * then remove the
     * convenience fee.
     */
    public function ConvenienceFeeRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.15 CustomerAccountsBalanceDetails
     * CustomerAccountsBalanceDetails is used to retrieve a tenant’s current balance
     * totals and payment
     * totals for each category (merchandise, deposit, rent, insurance, recurring,
     * late fees, and other fees).
     */
    public function CustomerAccountsBalanceDetails($iTenantID)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.16 CustomerAccountsBalanceDetailsWithPrepayment
     * CustomerAccountsBalanceDetailsWithPrepayment is used to retrieve a tenant’s
     * current balance totals
     * and payment totals for each category (merchandise, deposit, rent, insurance,
     * recurring, late fees, and
     * other fees) and allows an input value for the number of months to prepay.
     */
    public function CustomerAccountsBalanceDetailsWithPrepayment($iTenantID, $iNumberOfMonthsPrepay)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iNumberOfMonthsPrepay = $iNumberOfMonthsPrepay;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.17 DeliveryFeeRetrieve
     * DeliveryFeeRetrieve is used to retrieve the current delivery fee and tax rates
     * for that delivery fee for a
     * particular site.
     * Note: This is primarily used for Mobile Storage.
     */
    public function DeliveryFeeRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.18 DiscountPlansRetrieve
     * This method will return the available discount plans. The ConcessionPlans
     * table has all the sites discount data. The second table, ConcessionUnitTypes,
     * is used to determine if there are any unit type restrictions on the specified
     * ConcessionID. If no ConcessionUnitTypes records are found for a specific
     * ConcessionID
     * then that Concession plan can be used for all unit types and sizes. If 1 or
     * more
     * records for a specific ConcessionID are found then
     */
    public function DiscountPlansRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.19 EmployeeLogin
     * EmployeeLogin is used to verify an employee’s login credentials and will return
     * the associated
     * EmployeeID. This can be useful for applications outside of SiteLink which
     * require a login and you want
     * to use SiteLink’s employee table as the source.
     * Note: There is Hacking prevention built into this method. Only 3 invalid login
     * attempts can be
     * made before a 5 minute lockout period.
     */
    public function EmployeeLogin($sEmployeeLogin, $sEmployeePassword)
    {
        $params = new stdClass();
        $params->sEmployeeLogin = $sEmployeeLogin;
        $params->sEmployeePassword = $sEmployeePassword;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.20 InsuranceCoverageRetrieve
     * InsuranceCoverageRetrieve is used to retrieve the available insurance coverage
     * plans for a particular
     * site.
     */
    public function InsuranceCoverageRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.21 KeypadZonesRetrieve
     * KeypadZonesRetrieve is used to get a list of all keypad zones for a specified
     * location.
     * These are used in conjuncture with MoveInWithDiscount_v4 function.
     */
    public function KeypadZonesRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.22 LeadGeneration
     * LeadGeneration is used to create a lead for a site. This lead will be posted to
     * the specific site’s bulletin
     * board in SiteLink.
     */
    public function LeadGeneration($sFirstName, $sLastName, $sAddress1, $sAddress2, $sCity, $sState, $sZip, $sEmail, $sPhone, $sComments)
    {
        $params = new stdClass();
        $params->sFirstName = $sFirstName;
        $params->sLastName = $sLastName;
        $params->sAddress1 = $sAddress1;
        $params->sAddress2 = $sAddress2;
        $params->sCity = $sCity;
        $params->sState = $sState;
        $params->sZip = $sZip;
        $params->sEmail = $sEmail;
        $params->sPhone = $sPhone;
        $params->sComments = $sComments;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.23 LedgersByTenantID
     * LedgersByTenantID is used to retrieve all of the ledgers for a given tenant.
     */
    public function LedgersByTenantID($sTenantID)
    {
        $params = new stdClass();
        $params->sTenantID = $sTenantID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.24 LedgerStatementByLedgerID
     * LedgerStatementByLedgerID is used to retrieve the information contained in a
     * specific ledger.
     */
    public function LedgerStatementByLedgerID($sLedgerID)
    {
        $params = new stdClass();
        $params->sLedgerID = $sLedgerID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.25 MarketingSourcesRetrieve
     * MarketingSourcesRetrieve will return the available Marketing information for
     * the site.
     */
    public function MarketingSourcesRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.26 MoveIn
     * MoveIn is used to move a tenant into a specific unit. A full payment must be
     * made from this interface.
     * You should call MoveInCostRetrieve to get the total amount needed to move in.
     */
    public function MoveIn($TenantID, $sAccessCode, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $bTestMode)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->sAccessCode = $sAccessCode;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.27 MoveInCostRetrieve
     * MoveInCostRetrieve is used to retrieve the total amount that will be due to
     * move a tenant into a specific
     * unit on a specific date. This should be called before the MoveIn method to
     * ensure that the amount
     * passed into the MoveIn method and the amount calculated here match.
     */
    public function MoveInCostRetrieve($iUnitID, $dMoveInDate)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->dMoveInDate = $dMoveInDate;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.28 MoveInCostRetrieveWithDiscount
     * MoveInCostRetrieveWithDiscount is used to retrieve the total amount that will
     * be due to move a tenant
     * into a specific unit on a specific date. This should be called before the
     * MoveIn method to ensure that the
     * amount passed into the MoveIn method and the amount calculated here match.
     */
    public function MoveInCostRetrieveWithDiscount($iUnitID, $dMoveInDate, $InsuranceCoverageID, $ConcessionPlanID)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->dMoveInDate = $dMoveInDate;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.29 MoveInCostRetrieveWithDiscount_28DayBilling
     * MoveInCostRetrieveWithDiscount_28DayBilling is used to retrieve the total
     * amount that will be due to
     * move a tenant into a specific unit on a specific date with a billing frequency
     * of 28 days. This should be
     * called before the MoveIn method to ensure that the amount passed into the
     * MoveIn method and the
     * amount calculated here match.
     */
    public function MoveInCostRetrieveWithDiscount_28DayBilling($iUnitID, $dMoveInDate, $InsuranceCoverageID, $ConcessionPlanID)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->dMoveInDate = $dMoveInDate;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.30 MoveInCostRetrieveWithDiscount_28DayBilling_Reservation
     * MoveInCostRetrieveWithDiscount_28DayBilling_Reservation is used to retrieve the
     * total amount that will
     * be due to move a tenant into a specific unit on a specific date with a billing
     * frequency of 28 days. This
     * should be called before the MoveInReservation_28DayBilling method to ensure
     * that the amount passed
     * into the MoveInReservation_28DayBilling method and the amount calculated here
     * match.
     */
    public function MoveInCostRetrieveWithDiscount_28DayBilling_Reservation($iUnitID, $dMoveInDate, $InsuranceCoverageID, $ConcessionPlanID, $WaitingID)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->dMoveInDate = $dMoveInDate;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->WaitingID = $WaitingID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.31 MoveInCostRetrieveWithPushRate
     * MoveInCostRetrieveWithPushRate is used to retrieve the total amount that will
     * be due to move a tenant
     * into a specific unit on a specific date when forcing the push rate. This should
     * be called before the
     * MoveIn method to ensure that the amount passed into the MoveIn method and the
     * amount calculated
     * here match. Note that when calling MoveIn you will need to set the bUsePushRate
     * parameter to True.
     */
    public function MoveInCostRetrieveWithPushRate($iUnitID, $dMoveInDate, $InsuranceCoverageID, $ConcessionPlanID)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->dMoveInDate = $dMoveInDate;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.32 MoveInCostRetrieve_28DayBilling
     * MoveInCostRetrieve_28DayBilling is used to retrieve the total amount that will
     * be due to move a tenant
     * into a specific unit on a specific date at a billing frequency of 28 days. This
     * should be called before the
     * MoveIn method to ensure that the amount passed into the MoveIn method and the
     * amount calculated
     * here match.
     */
    public function MoveInCostRetrieve_28DayBilling($iUnitID, $dMoveInDate)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->dMoveInDate = $dMoveInDate;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.33 MoveInReservation
     * MoveInReservation is used to move a tenant with a pre-existing reservation into
     * a unit. A full payment
     * must be made from this interface. You should call MoveInCostRetrieve to get the
     * total amount needed
     * to move in.
     */
    public function MoveInReservation($WaitingID, $TenantID, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $InsuranceCoverageID, $ConcessionPlanID, $bTestMode)
    {
        $params = new stdClass();
        $params->WaitingID = $WaitingID;
        $params->TenantID = $TenantID;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.34 MoveInReservation_28DayBilling
     * MoveInReservation_28DayBilling is used to move a tenant with a pre-existing
     * reservation into a unit with
     * a 28 day billing cycle. A full payment must be made from this interface. You
     * should call
     * MoveInCostRetrieveWithDiscount_28DayBilling_Reservation to get the total amount
     * needed to move in.
     */
    public function MoveInReservation_28DayBilling($WaitingID, $TenantID, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $InsuranceCoverageID, $ConcessionPlanID, $bTestMode)
    {
        $params = new stdClass();
        $params->WaitingID = $WaitingID;
        $params->TenantID = $TenantID;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.35 MoveInWithDiscount
     * MoveIn is used to move a tenant into a specific unit. A full payment must be
     * made from this interface.
     * You should call MoveInCostRetrieve to get the total amount needed to move in.
     */
    public function MoveInWithDiscount($TenantID, $sAccessCode, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $InsuranceCoverageID, $ConcessionPlanID, $bTestMode)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->sAccessCode = $sAccessCode;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.36 MoveInWithDiscount_v2
     * MoveInWithDiscount_v2 is used to move a tenant into a specific unit. Version 2
     * also allows for the
     * source to be specified and the push rate to be used. A full payment must be
     * made from this interface.
     * You should call MoveInCostRetrieve to get the total amount needed to move in.
     */
    public function MoveInWithDiscount_v2($TenantID, $sAccessCode, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $InsuranceCoverageID, $ConcessionPlanID, $iSource, $sSource, $bUsePushRate, $bTestMode)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->sAccessCode = $sAccessCode;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->iSource = $iSource;
        $params->sSource = $sSource;
        $params->bUsePushRate = $bUsePushRate;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.37 MoveInWithDiscount_v3
     * MoveInWithDiscount_v3 is used to move a tenant into a specific unit. Version 3
     * also allows for the
     * payment method to be specified. You are able to make payments via Credit Card or
     * ACH using this
     * function. The appropriate data must also be provided. A full payment must be made
     * from this interface.
     * You should call MoveInCostRetrieve to get the total amount needed to move in.*/
    public function MoveInWithDiscount_v3($TenantID, $sAccessCode, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $InsuranceCoverageID, $ConcessionPlanID, $iSource, $sSource, $bUsePushRate, $iPayMethod, $sABARoutingNum, $sAccountNum, $iAccountType, $bTestMode)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->sAccessCode = $sAccessCode;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->iSource = $iSource;
        $params->sSource = $sSource;
        $params->bUsePushRate = $bUsePushRate;
        $params->iPayMethod = $iPayMethod;
        $params->sABARoutingNum = $sABARoutingNum;
        $params->sAccountNum = $sAccountNum;
        $params->iAccountType = $iAccountType;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.38 MoveInWithDiscount_v4
     * MoveInWithDiscount_v4 is used to move a tenant into a specific unit. Version 4
     * also allows for the
     * keypad zone, time zone and billing frequency to be set at move in. Keypad and
     * time zone ID’s can be
     * retrieve via the appropriate retrieval functions (KeypadZonesRetrieve and
     * TimeZonesRetrieve).
     * A full payment must be made from this interface. You should call the
     * appropriate MoveInCostRetrieve
     * function based on billing frequency to get the total amount needed to move in.
     */
    public function MoveInWithDiscount_v4($TenantID, $sAccessCode, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $sCCTrack2, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $InsuranceCoverageID, $ConcessionPlanID, $iSource, $sSource, $bUsePushRate, $iPayMethod, $sABARoutingNum, $sAccountNum, $iAccountType, $iKeypadZoneID, $iTimeZoneID, $iBillingFrequency, $WaitingID, $bTestMode)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->sAccessCode = $sAccessCode;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->sCCTrack2 = $sCCTrack2;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->iSource = $iSource;
        $params->sSource = $sSource;
        $params->bUsePushRate = $bUsePushRate;
        $params->iPayMethod = $iPayMethod;
        $params->sABARoutingNum = $sABARoutingNum;
        $params->sAccountNum = $sAccountNum;
        $params->iAccountType = $iAccountType;
        $params->iKeypadZoneID = $iKeypadZoneID;
        $params->iTimeZoneID = $iTimeZoneID;
        $params->iBillingFrequency = $iBillingFrequency;
        $params->WaitingID = $WaitingID;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.39 MoveInWithDiscount_28DayBilling
     * MoveInWithDiscount_28DayBilling is used to move a tenant into a specific unit
     * at a billing frequency of
     * 28 days. A full payment must be made from this interface. You should call
     * MoveInCostRetrieve to get
     * the total amount needed to move in.
     */
    public function MoveInWithDiscount_28DayBilling($TenantID, $sAccessCode, $UnitID, $dStartDate, $dEndDate, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $InsuranceCoverageID, $ConcessionPlanID, $bTestMode)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->sAccessCode = $sAccessCode;
        $params->UnitID = $UnitID;
        $params->dStartDate = $dStartDate;
        $params->dEndDate = $dEndDate;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.40 NationalMasterAccountsRetrieve
     * NationalMasterAccountsRetrieve is used to get the national master account
     * record for the specified site
     * ID. The search performed will do an OR search based on the account name and
     * account number
     * specified.
     */
    public function NationalMasterAccountsRetrieve($sAccountName, $sAccountNum)
    {
        $params = new stdClass();
        $params->sAccountName = $sAccountName;
        $params->sAccountNum = $sAccountNum;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.41 PaidThroughDateByLedgerID
     * PaidThroughDateByLedgerID is used to retrieve the paid through date contained
     * in a specific ledger.
     * This date can also be used to determine how many days a payment is delinquent.
     */
    public function PaidThroughDateByLedgerID($iLedgerID)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.42 PaymentsByLedgerID
     * PaymentsByLedgerID is used to retrieve all payments that a given tenant has
     * performed.
     */
    public function PaymentsByLedgerID($sLedgerID)
    {
        $params = new stdClass();
        $params->sLedgerID = $sLedgerID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.43 PaymentSettings
     * PaymentSettings is used to get the number of days past due at which online
     * payments is disabled for
     * tenants. This setting can be modified in the Web Template Configuration form.
     */
    public function PaymentSettings()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.44 PaymentSimple
     * PaymentSimple is used to make a payment for a specific unit’s rent charges and
     * other fees.
     */
    public function PaymentSimple($iTenantID, $iUnitID, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $bTestMode)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.45 PaymentSimpleACH
     * PaymentSimpleACH is used to make an ACH payment for a specific unit’s rent
     * charges and other fees.
     */
    public function PaymentSimpleACH($iTenantID, $iLedgerID, $dcPaymentAmount, $sABARoutingNum, $sAccountNum, $iAccountType)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iLedgerID = $iLedgerID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->sABARoutingNum = $sABARoutingNum;
        $params->sAccountNum = $sAccountNum;
        $params->iAccountType = $iAccountType;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.46 PaymentSimpleACHWithSource
     * PaymentSimpleACHWithSource is used to make an ACH payment for a specific unit’s
     * rent
     * charges and other fees.
     */
    public function PaymentSimpleACHWithSource($iTenantID, $iLedgerID, $dcPaymentAmount, $sABARoutingNum, $sAccountNum, $iAccountType, $iSource)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iLedgerID = $iLedgerID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->sABARoutingNum = $sABARoutingNum;
        $params->sAccountNum = $sAccountNum;
        $params->iAccountType = $iAccountType;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.47 PaymentSimpleCash
     * PaymentSimpleCash is used to make a CASH ONLY payment for a specific unit’s
     * rent charges and
     * other fees.
     */
    public function PaymentSimpleCash($iTenantID, $iUnitID, $dcPaymentAmount)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.48 PaymentSimpleCashWithSource
     * PaymentSimpleCashWithSource is used to make a CASH ONLY payment for a specific
     * unit’s rent
     * charges and other fees.
     */
    public function PaymentSimpleCashWithSource($iTenantID, $iUnitID, $dcPaymentAmount, $iSource)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.49 PaymentSimpleCheck
     * PaymentSimpleCheck is used to make a check payment for a specific unit’s rent
     * charges and other fees.
     */
    public function PaymentSimpleCheck($iTenantID, $iUnitID, $dcPaymentAmount, $sCheckNumber)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->sCheckNumber = $sCheckNumber;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.50 PaymentSimpleCheckWithSourcek
     * PaymentSimpleCheckWithSource is used to make a check payment for a specific
     * unit’s rent charges and other fees.
     */
    public function PaymentSimpleCheckWithSource($iTenantID, $iUnitID, $dcPaymentAmount, $sCheckNumber, $iSource)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->sCheckNumber = $sCheckNumber;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.51 PaymentSimpleWithSource
     * PaymentSimple is used to make a payment for a specific unit’s rent charges and
     * other fees.
     */
    public function PaymentSimpleWithSource($iTenantID, $iUnitID, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $bTestMode, $iSource)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->bTestMode = $bTestMode;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.52 PaymentTypesRetrieve
     * PaymentTypesRetrieve is used to get the supported credit card payment types and
     * their IDs (VISA,
     * AMEX, etc.). The ID must be passed into the TenantBillingInfoUpdate method if
     * credit card payments
     * are to be used.
     */
    public function PaymentTypesRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.53 POSItemAddToLedger
     * POSItemAddToLedger is used to add a POS item to a specified ledger. This
     * function gives you the
     * option to set a custom pre tax price for the item being added. You also have
     * the ability to adjust the
     * quantity of the item that is added.
     */
    public function POSItemAddToLedger($LedgerID, $ChargeDescID, $dcPricePreTax, $iQuantity)
    {
        $params = new stdClass();
        $params->LedgerID = $LedgerID;
        $params->ChargeDescID = $ChargeDescID;
        $params->dcPricePreTax = $dcPricePreTax;
        $params->iQuantity = $iQuantity;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.54 POSItemPayment
     * POSItemPayment is used to make a POS payment for merchandise. The charge
     * description ID
     * parameter is able to take in a comma delimited list of ID’s. In order to pay
     * for multiple quantities of a
     * particular item you can simply include the ID the number of times that is
     * needed (ex string ‘160, 160,
     * 160, 172’).
     */
    public function POSItemPayment($sChargeDescID, $dcPaymentAmount, $iPaymentMethod, $sCheckNum, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $iSource, $bTestMode)
    {
        $params = new stdClass();
        $params->sChargeDescID = $sChargeDescID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iPaymentMethod = $iPaymentMethod;
        $params->sCheckNum = $sCheckNum;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->iSource = $iSource;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.55 POSItemsRetrieve
     * POSItemsRetrieve is used to retrieve information on the POS items available for
     * a facility.
     * Note: There is NOT a method that will allow you to reserve or purchase POS
     * items via the API. This API
     * method can be used to display the description, pricing, and availability of POS
     * items. One option is to
     * enter the POS item requested in the notes section of a reservation.
     */
    public function POSItemsRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.56 POSItemUpdateInStockQuantity
     * POSItemUpdateInStockQuantity
     */
    public function POSItemUpdateInStockQuantity($ChargeDescID, $dcInStock)
    {
        $params = new stdClass();
        $params->ChargeDescID = $ChargeDescID;
        $params->dcInStock = $dcInStock;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.57 PromotionsRetrieve
     * This method will return the available promotions. The flow should start with
     * the Promotions table and
     * inner join on the IDs until the ConcessionPlans table is reached. The
     * ConcessionPlans table will contain
     * the discount information for each discount under the promotion.
     * Updates (01/13/2011): Added iAvailableAt to the ConcessionPlans table and added
     * the
     * ConcessionUnitTypes table. If not ConcessionUnitTypes records are found for a
     * specific ConcessionID
     * then that Concession plan can be used for all unit types and sizes. If 1 or
     * more records for a specific
     * ConcessionID are found then those Unit Types and Unit Sizes are the only ones
     * available to use the
     * specified Concession plan.
     */
    public function PromotionsRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.58 ProrationInformationRetrieve
     * ProrationInformationRetrieve is used to get the current proration information
     * for a particular site. These
     * values can be edited through the ‘Program Defaults’ screen.
     */
    public function ProrationInformationRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.59 PurchaseOrderNumberRetrieve
     * PurchaseOrderNumberRetrieve is used to get the purchase order number for a
     * specified unit.
     */
    public function PurchaseOrderNumberRetrieve($iUnitID)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.60 PurchaseOrderNumberUpdate
     * PurchaseOrderNumberUpdate is used to update the purchase order number for a
     * specified unit.
     */
    public function PurchaseOrderNumberUpdate($iUnitID, $sPurchaseOrderNumber)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->sPurchaseOrderNumber = $sPurchaseOrderNumber;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.61 RentTaxRatesRetrieve
     * RentTaxRatesRetrieve is used to retrieve the current tax rates associated with
     * rent for a particular site.
     */
    public function RentTaxRatesRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.62 ReservationBillingInfoByTenantID
     */
    public function ReservationBillingInfoByTenantID($iTenantID)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.63 ReservationBillingInfoUpdate
     */
    public function ReservationBillingInfoUpdate($iWaitingID, $iCreditCardTypeID, $sCreditCardNum, $dCredtiCardExpir, $sCreditCardHolderName, $sCreditCardStreet, $sCreditCardZip)
    {
        $params = new stdClass();
        $params->iWaitingID = $iWaitingID;
        $params->iCreditCardTypeID = $iCreditCardTypeID;
        $params->sCreditCardNum = $sCreditCardNum;
        $params->dCredtiCardExpir = $dCredtiCardExpir;
        $params->sCreditCardHolderName = $sCreditCardHolderName;
        $params->sCreditCardStreet = $sCreditCardStreet;
        $params->sCreditCardZip = $sCreditCardZip;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.64 ReservationFeeAdd
     * ReservationFeeAdd is used to add a reservation fee to a newly created
     * reservation. This method will
     * automatically process the credit card for the reservation fee amount. Please
     * call
     * ReservationFeeRetrieve first to ensure the correct amount will be charged.
     */
    public function ReservationFeeAdd($iTenantID, $iWaitingListID, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $bTestMode)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iWaitingListID = $iWaitingListID;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.65 ReservationFeeAddWithSource
     * ReservationFeeAddWithSource is used to add a reservation fee to a newly created
     * reservation. This
     * method will automatically process the credit card for the reservation fee
     * amount. Please call
     * ReservationFeeRetrieve first to ensure the correct amount will be charged.
     */
    public function ReservationFeeAddWithSource($iTenantID, $iWaitingListID, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $bTestMode, $iSource)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iWaitingListID = $iWaitingListID;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->bTestMode = $bTestMode;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.66 ReservationFeeRetrieve
     * ReservationFeeRetrieve is used to retrieve the current reservation fee for a
     * particular site.
     */
    public function ReservationFeeRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.67 ReservationList
     * ReservationList is used to get all of the current reservations that a site has
     * pending. The method can
     * either pull all reservations or it can get just one.
     */
    public function ReservationList($iGlobalWaitingNum)
    {
        $params = new stdClass();
        $params->iGlobalWaitingNum = $iGlobalWaitingNum;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.68 ReservationListByTenant
     * ReservationListByTenant is used to get all of the current reservations that a
     * site has pending for a
     * specific tenant.
     */
    public function ReservationListByTenantID($TenantID)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.69 ReservationNew
     * ReservationNew is used to create a new reservation for a tenant. The tenant
     * either has to be in the
     * system already or has to be created first before calling this method. When this
     * method is called the
     * tenant will be placed directly on the Waiting List in SiteLink and will NOT be
     * put on the bulletin board.
     */
    public function ReservationNew($sTenantID, $sUnitID1, $sUnitID2, $sUnitID3, $dNeeded, $sComment)
    {
        $params = new stdClass();
        $params->sTenantID = $sTenantID;
        $params->sUnitID1 = $sUnitID1;
        $params->sUnitID2 = $sUnitID2;
        $params->sUnitID3 = $sUnitID3;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.70 ReservationNewWithSource
     * ReservationNewWithSource is used to create a new reservation for a tenant with
     * the source included for
     * tracking purposes. The tenant either has to be in the system already or has to
     * be created first before
     * calling this method. When this method is called the tenant will be placed
     * directly on the Waiting List in
     * SiteLink and will NOT be put on the bulletin board.
     */
    public function ReservationNewWithSource($sTenantID, $sUnitID1, $sUnitID2, $sUnitID3, $dNeeded, $sComment, $iSource)
    {
        $params = new stdClass();
        $params->sTenantID = $sTenantID;
        $params->sUnitID1 = $sUnitID1;
        $params->sUnitID2 = $sUnitID2;
        $params->sUnitID3 = $sUnitID3;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.71 ReservationNewWithSource_v2
     * ReservationNewWithSource_v2 is used to create a new reservation for a tenant
     * with the source and type
     * of inquiry included for tracking purposes. The tenant either has to be in the
     * system already or has to be
     * created first before calling this method. When this method is called the tenant
     * will be placed directly on
     * the Waiting List in SiteLink and will NOT be put on the bulletin board.
     */
    public function ReservationNewWithSource_v2($sTenantID, $sUnitID, $dNeeded, $sComment, $iSource, $sSource, $QTRentalTypeID, $iInquiryType)
    {
        $params = new stdClass();
        $params->sTenantID = $sTenantID;
        $params->sUnitID = $sUnitID;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iSource = $iSource;
        $params->sSource = $sSource;
        $params->QTRentalTypeID = $QTRentalTypeID;
        $params->iInquiryType = $iInquiryType;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.72 ReservationNewWithSource_v3
     * ReservationNewWithSource_v3 is used to create a new reservation for a tenant
     * with the source and type
     * of inquiry included for tracking purposes. This method also allows the quoted
     * rate to be set and the date
     * at which the reservation expires. The tenant either has to be in the system
     * already or has to be created
     * first before calling this method. When this method is called the tenant will be
     * placed directly on the
     * Waiting List in SiteLink and will NOT be put on the bulletin board.
     */
    public function ReservationNewWithSource_v3($sTenantID, $sUnitID, $dNeeded, $sComment, $iSource, $sSource, $QTRentalTypeID, $iInquiryType, $dcQuotedRate, $dExpires)
    {
        $params = new stdClass();
        $params->sTenantID = $sTenantID;
        $params->sUnitID = $sUnitID;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iSource = $iSource;
        $params->sSource = $sSource;
        $params->QTRentalTypeID = $QTRentalTypeID;
        $params->iInquiryType = $iInquiryType;
        $params->dcQuotedRate = $dcQuotedRate;
        $params->dExpires = $dExpires;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.73 ReservationNewWithSource_v4
     * ReservationNewWithSource_v4 is used to create a new reservation for a tenant
     * with the source and type
     * of inquiry included for tracking purposes. This method also allows the quoted
     * rate to be set and the date
     * at which the reservation expires. The tenant either has to be in the system
     * already or has to be created
     * first before calling this method. When this method is called the tenant will be
     * placed directly on the
     * Waiting List in SiteLink and will NOT be put on the bulletin board.
     */
    public function ReservationNewWithSource_v4($sTenantID, $sUnitID, $dNeeded, $sComment, $iSource, $sSource, $QTRentalTypeID, $iInquiryType, $dcQuotedRate, $dExpires, $dFollowUp)
    {
        $params = new stdClass();
        $params->sTenantID = $sTenantID;
        $params->sUnitID = $sUnitID;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iSource = $iSource;
        $params->sSource = $sSource;
        $params->QTRentalTypeID = $QTRentalTypeID;
        $params->iInquiryType = $iInquiryType;
        $params->dcQuotedRate = $dcQuotedRate;
        $params->dExpires = $dExpires;
        $params->dFollowUp = $dFollowUp;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.74 ReservationNoteInsert
     * ReservationNoteInsert is used to add a note to a reservation.
     */
    public function ReservationNoteInsert($WaitingID, $sNote)
    {
        $params = new stdClass();
        $params->WaitingID = $WaitingID;
        $params->sNote = $sNote;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.75 ReservationNotesRetrieve
     *
     */
    public function ReservationNotesRetrieve($WaitingID)
    {
        $params = new stdClass();
        $params->WaitingID = $WaitingID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.77 ReservationUpdate
     * ReservationUpdate is used to update an existing reservation.
     */
    public function ReservationUpdate($WaitingID, $sTenantID, $sUnitID, $dNeeded, $sComment, $iStatus)
    {
        $params = new stdClass();
        $params->WaitingID = $WaitingID;
        $params->sTenantID = $sTenantID;
        $params->sUnitID = $sUnitID;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iStatus = $iStatus;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.78 ReservationUpdate_v2
     * ReservationUpdate is used to update an existing reservation.
     */
    public function ReservationUpdate_v2($WaitingID, $sTenantID, $sUnitID, $dNeeded, $sComment, $iStatus, $bFollowup, $dFollowup, $dFollowupLast, $iInquiryType, $dcQuotedRate, $dExpires)
    {
        $params = new stdClass();
        $params->WaitingID = $WaitingID;
        $params->sTenantID = $sTenantID;
        $params->sUnitID = $sUnitID;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iStatus = $iStatus;
        $params->bFollowup = $bFollowup;
        $params->dFollowup = $dFollowup;
        $params->dFollowupLast = $dFollowupLast;
        $params->iInquiryType = $iInquiryType;
        $params->dcQuotedRate = $dcQuotedRate;
        $params->dExpires = $dExpires;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.79 ReservationUpdate_v3
     * ReservationUpdate is used to update an existing reservation.
     */
    public function ReservationUpdate_v3($WaitingID, $sTenantID, $sUnitID, $dNeeded, $sComment, $iStatus, $bFollowup, $dFollowup, $dFollowupLast, $iInquiryType, $dcQuotedRate, $dExpires, $QTRentalTypeID, $QTCancellationTypeID, $sCancellationReason)
    {
        $params = new stdClass();
        $params->WaitingID = $WaitingID;
        $params->sTenantID = $sTenantID;
        $params->sUnitID = $sUnitID;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iStatus = $iStatus;
        $params->bFollowup = $bFollowup;
        $params->dFollowup = $dFollowup;
        $params->dFollowupLast = $dFollowupLast;
        $params->iInquiryType = $iInquiryType;
        $params->dcQuotedRate = $dcQuotedRate;
        $params->dExpires = $dExpires;
        $params->QTRentalTypeID = $QTRentalTypeID;
        $params->QTCancellationTypeID = $QTCancellationTypeID;
        $params->sCancellationReason = $sCancellationReason;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.80 ScheduleMoveOut
     * ScheduleMoveOut is used to set the scheduled move out date for the specified
     * ledger. Note that this
     * function will not actually move out a unit, it will only set the scheduled move
     * out date.
     */
    public function ScheduleMoveOut($iLedgerID, $dScheduledOut)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        $params->dScheduledOut = $dScheduledOut;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.81 ScheduleTenantRateChange
     * SchedulteTenantRateChange is used to schedule a future tenant rate adjustment.
     * A tenant note will also
     * be created that will track the previous rate as well as when the future rate is
     * to take effect.
     */
    public function ScheduleTenantRateChange($LedgerID, $dcNewRate, $dScheduledChange)
    {
        $params = new stdClass();
        $params->LedgerID = $LedgerID;
        $params->dcNewRate = $dcNewRate;
        $params->dScheduledChange = $dScheduledChange;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.82 SiteInformation
     * SiteInformation is used to retrieve general information about a specific site
     * (address, phone number,
     * etc.).
     */
    public function SiteInformation()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.83 SiteSearchByPostalCode
     * SiteSearchByPostalCode is used to retrieve the site locations under a specific
     * corporate code and their
     * respective distances from a reference postal code. If the postal code is not
     * found the distances will be
     * returned with a value of zero.
     * Note: Currently this method only works for sites in the US
     */
    public function SiteSearchByPostalCode($sPostalCode, $iCountry, $bMiles)
    {
        $params = new stdClass();
        $params->sPostalCode = $sPostalCode;
        $params->iCountry = $iCountry;
        $params->bMiles = $bMiles;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.84 TenantBillingInfoByTenantID
     * TenantBillingInfoByTenantID is used to get a tenant’s billing information (both
     * Credit Card and ACH) for
     * each of their active ledgers. The second table returned is the same as the
     * return from
     * PaymentTypesRetrieve in order to consolidate the information into one web
     * service call.
     * Note: Please follow all PCI DSS Compliance rules for handling this non-masked
     * credit card
     * information.
     */
    public function TenantBillingInfoByTenantID($iTenantID)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.85 TenantBillingInfoUpdate
     * TenantBillingInfoUpdate is used to update a tenant’s stored Credit Card, ACH,
     * or auto bill preference
     * information.
     */
    public function TenantBillingInfoUpdate($iLedgerID, $iCreditCardTypeID, $sCreditCardNum, $dCredtiCardExpir, $sCreditCardHolderName, $sCreditCardStreet, $sCreditCardZip, $iAutoBillType, $sACH_CheckWriterAcctNum, $sACH_ABA_RoutingNum, $sACH_Check_SavingsCode)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        $params->iCreditCardTypeID = $iCreditCardTypeID;
        $params->sCreditCardNum = $sCreditCardNum;
        $params->dCredtiCardExpir = $dCredtiCardExpir;
        $params->sCreditCardHolderName = $sCreditCardHolderName;
        $params->sCreditCardStreet = $sCreditCardStreet;
        $params->sCreditCardZip = $sCreditCardZip;
        $params->iAutoBillType = $iAutoBillType;
        $params->sACH_CheckWriterAcctNum = $sACH_CheckWriterAcctNum;
        $params->sACH_ABA_RoutingNum = $sACH_ABA_RoutingNum;
        $params->sACH_Check_SavingsCode = $sACH_Check_SavingsCode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.86 TenantExitSurveyUpdate
     * TenantExitSurveyUpdate is used to update a tenant's exit survey information.
     * You must first call
     * TenantList to find the tenant’s ID which has to be passed in as a parameter.
     * Whatever parameters are
     * passed into this method will overwrite the existing stored information. If you
     * do not wish to update a
     * section you must pass in the original data retrieved when calling
     * TenantInfoByTenantID for that tenant.
     */
    public function TenantExitSurveyUpdate($TenantID, $bExit_OnEmailOfferList, $dExit_WhenNeedAgain, $MktgExitRentAgainID, $MktgExitReasonID, $MktgExitSatisfactionID, $iExitSat_Cleanliness, $iExitSat_Safety, $iExitSat_Services, $iExitSat_Staff, $iExitSat_Price, $sExitComment)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->bExit_OnEmailOfferList = $bExit_OnEmailOfferList;
        $params->dExit_WhenNeedAgain = $dExit_WhenNeedAgain;
        $params->MktgExitRentAgainID = $MktgExitRentAgainID;
        $params->MktgExitReasonID = $MktgExitReasonID;
        $params->MktgExitSatisfactionID = $MktgExitSatisfactionID;
        $params->iExitSat_Cleanliness = $iExitSat_Cleanliness;
        $params->iExitSat_Safety = $iExitSat_Safety;
        $params->iExitSat_Services = $iExitSat_Services;
        $params->iExitSat_Staff = $iExitSat_Staff;
        $params->iExitSat_Price = $iExitSat_Price;
        $params->sExitComment = $sExitComment;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.87 TenantIDByUnitNameOrAccessCode
     */
    public function TenantIDByUnitNameOrAccessCode($sUnitName, $sAccessCode)
    {
        $params = new stdClass();
        $params->sUnitName = $sUnitName;
        $params->sAccessCode = $sAccessCode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.88 TenantInfoByTenantID
     * TenantInfoByTenantID is used to retrieve all stored information in the tenants
     * table for a given tenant.
     */
    public function TenantInfoByTenantID($iTenantID)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.89 TenantList
     * TenantList is used to get all of the tenants that match the given inputs. If no
     * location code is provided
     * the tenant search will search across all sites for a given corporate code. This
     * method also returns
     * tenants who do not match the search parameters exactly.
     * Example: If you search for “Jo Smi” you will get results like “John Smith”
     * Note: This method will only return a maximum of 50 tenants to avoid
     * transmitting too much data
     */
    public function TenantList($sTenantFirstName, $sTenantLastName)
    {
        $params = new stdClass();
        $params->sTenantFirstName = $sTenantFirstName;
        $params->sTenantLastName = $sTenantLastName;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.90 TenantListDetailed
     * TenantListDetailed is used to get all of the tenants that match the given
     * inputs. If no location code is
     * provided the tenant search will search across all sites for a given corporate
     * code. This method also
     * returns tenants who do not match the search parameters exactly.
     * Example: If you search for “Jo Smi” you will get results like “John Smith”
     * Note: This method will only return a maximum of 50 tenants to avoid
     * transmitting too much data
     */
    public function TenantListDetailed($sTenantFirstName, $sTenantLastName, $sAddressLine1, $sAddressLine2, $sCity, $sState, $sZipCode, $sEmailAddress, $sPhoneNumber)
    {
        $params = new stdClass();
        $params->sTenantFirstName = $sTenantFirstName;
        $params->sTenantLastName = $sTenantLastName;
        $params->sAddressLine1 = $sAddressLine1;
        $params->sAddressLine2 = $sAddressLine2;
        $params->sCity = $sCity;
        $params->sState = $sState;
        $params->sZipCode = $sZipCode;
        $params->sEmailAddress = $sEmailAddress;
        $params->sPhoneNumber = $sPhoneNumber;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.91 TenantListDetailedMovedInTenantsOnly
     * TenantListDetailedMovedInTenantsOnly is used to get all of the tenants that
     * match the given inputs.
     * This version of the TenantListDetailed method will only return moved in
     * tenants. If no location code is
     * provided the tenant search will search across all sites for a given corporate
     * code. This method also
     * returns tenants who do not match the search parameters exactly.
     * Example: If you search for “Jo Smi” you will get results like “John Smith”
     * Note: This method will only return a maximum of 50 tenants to avoid
     * transmitting too much data
     */
    public function TenantListDetailedMovedInTenantsOnly($sTenantFirstName, $sTenantLastName, $sAddressLine1, $sAddressLine2, $sCity, $sState, $sZipCode, $sEmailAddress, $sPhoneNumber, $sCompany, $sUnitName, $sAccessCode)
    {
        $params = new stdClass();
        $params->sTenantFirstName = $sTenantFirstName;
        $params->sTenantLastName = $sTenantLastName;
        $params->sAddressLine1 = $sAddressLine1;
        $params->sAddressLine2 = $sAddressLine2;
        $params->sCity = $sCity;
        $params->sState = $sState;
        $params->sZipCode = $sZipCode;
        $params->sEmailAddress = $sEmailAddress;
        $params->sPhoneNumber = $sPhoneNumber;
        $params->sCompany = $sCompany;
        $params->sUnitName = $sUnitName;
        $params->sAccessCode = $sAccessCode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.92 TenantListDetailed_v2
     * TenantListDetailed_v2 is used to get all of the tenants that match the given
     * inputs. Version 2 includes
     * that addition of the company name as a search option. If no location code is
     * provided the tenant search
     * will search across all sites for a given corporate code. This method also
     * returns tenants who do not
     * match the search parameters exactly.
     * Example: If you search for “Jo Smi” you will get results like “John Smith”
     * Note: This method will only return a maximum of 50 tenants to avoid
     * transmitting too much data
     */
    public function TenantListDetailed_v2($sTenantFirstName, $sTenantLastName, $sAddressLine1, $sAddressLine2, $sCity, $sState, $sZipCode, $sEmailAddress, $sPhoneNumber, $sCompany)
    {
        $params = new stdClass();
        $params->sTenantFirstName = $sTenantFirstName;
        $params->sTenantLastName = $sTenantLastName;
        $params->sAddressLine1 = $sAddressLine1;
        $params->sAddressLine2 = $sAddressLine2;
        $params->sCity = $sCity;
        $params->sState = $sState;
        $params->sZipCode = $sZipCode;
        $params->sEmailAddress = $sEmailAddress;
        $params->sPhoneNumber = $sPhoneNumber;
        $params->sCompany = $sCompany;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.93 TenantLogin
     * TenantLogin is used to verify a tenant’s login credentials (email address and
     * password) and will return
     * the associated TenantID.
     */
    public function TenantLogin($sTenantLogin, $sTenantPassword)
    {
        $params = new stdClass();
        $params->sTenantLogin = $sTenantLogin;
        $params->sTenantPassword = $sTenantPassword;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.94 TenantLoginAndSecurityUpdate
     * TenantLoginAndSecurityUpdate is used to update a specified tenants web
     * password, security question
     * and answer. Note that web passwords should be 4-10 characters in length.
     */
    public function TenantLoginAndSecurityUpdate($TenantID, $sEmail, $sWebPassword, $sWebSecurityQ, $sWebSecurityQA)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->sEmail = $sEmail;
        $params->sWebPassword = $sWebPassword;
        $params->sWebSecurityQ = $sWebSecurityQ;
        $params->sWebSecurityQA = $sWebSecurityQA;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.95 TenantMarketingUpdate
     * TenantMarketingUpdate is used to update an existing tenant's marketing
     * information. You must first call
     * TenantList to find the tenant’s ID which has to be passed in as a parameter.
     * Whatever parameters are
     * passed into this method will overwrite the existing stored information. If you
     * do not wish to update a
     * section you must pass in the original data retrieved when calling
     * TenantInfoByTenantID for that tenant.
     */
    public function TenantMarketingUpdate($TenantID, $lngDOB, $iGender, $iMarketingID, $iMktgDistanceID, $iMktgReasonID, $iMktgTypeID, $iMktgWhatID, $iMktgWhyID, $iHowManyOtherStorageCosDidYouContact, $iUsedSelfStorageInThePast)
    {
        $params = new stdClass();
        $params->TenantID = $TenantID;
        $params->lngDOB = $lngDOB;
        $params->iGender = $iGender;
        $params->iMarketingID = $iMarketingID;
        $params->iMktgDistanceID = $iMktgDistanceID;
        $params->iMktgReasonID = $iMktgReasonID;
        $params->iMktgTypeID = $iMktgTypeID;
        $params->iMktgWhatID = $iMktgWhatID;
        $params->iMktgWhyID = $iMktgWhyID;
        $params->iHowManyOtherStorageCosDidYouContact = $iHowManyOtherStorageCosDidYouContact;
        $params->iUsedSelfStorageInThePast = $iUsedSelfStorageInThePast;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.96 TenantNew
     * TenantNew is used to create a new basic tenant with only a first name
     * and a last name for means of
     * creating a new reservation.
     */
    public function TenantNew($sTenantFirstName, $sTenantLastName)
    {
        $params = new stdClass();
        $params->sTenantFirstName = $sTenantFirstName;
        $params->sTenantLastName = $sTenantLastName;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.97 TenantNewDetailed
     * TenantNewDetailed is used to create a new tenant with all of the information
     * available in the tenant’s
     * table.
     * Note: This does NOT include credit card information. If you wish to store
     * credit card information before
     * moving a tenant in you can store it in the comments section for that tenant.
     */
    public function TenantNewDetailed($sWebPassword, $sMrMrs, $sFName, $sMI, $sLName, $sCompany, $sAddr1, $sAddr2, $sCity, $sRegion, $sPostalCode, $sCountry, $sPhone, $sMrMrsAlt = '', $sFNameAlt = '', $sMIAlt = '', $sLNameAlt = '', $sAddr1Alt = '', $sAddr2Alt = '', $sCityAlt = '', $sRegionAlt = '', $sPostalCodeAlt = '', $sCountryAlt = '', $sPhoneAlt = '', $sMrMrsBus = '', $sFNameBus = '', $sMIBus = '', $sLNameBus = '', $sCompanyBus = '', $sAddr1Bus = '', $sAddr2Bus = '', $sCityBus = '', $sRegionBus = '', $sPostalCodeBus = '', $sCountryBus = '', $sPhoneBus = '', $sFax = '', $sEmail = '', $sPager = '', $sMobile = '', $bCommercial = '', $bCompanyIsTenant = '', $dDOB = '', $sTenNote = '', $sLicense = '', $sLicRegion = '', $sSSN = '')
    {
        $params = new stdClass();
        $params->sWebPassword = $sWebPassword;
        $params->sMrMrs = $sMrMrs;
        $params->sFName = $sFName;
        $params->sMI = $sMI;
        $params->sLName = $sLName;
        $params->sCompany = $sCompany;
        $params->sAddr1 = $sAddr1;
        $params->sAddr2 = $sAddr2;
        $params->sCity = $sCity;
        $params->sRegion = $sRegion;
        $params->sPostalCode = $sPostalCode;
        $params->sCountry = $sCountry;
        $params->sPhone = $sPhone;
        $params->sMrMrsAlt = $sMrMrsAlt;
        $params->sFNameAlt = $sFNameAlt;
        $params->sMIAlt = $sMIAlt;
        $params->sLNameAlt = $sLNameAlt;
        $params->sAddr1Alt = $sAddr1Alt;
        $params->sAddr2Alt = $sAddr2Alt;
        $params->sCityAlt = $sCityAlt;
        $params->sRegionAlt = $sRegionAlt;
        $params->sPostalCodeAlt = $sPostalCodeAlt;
        $params->sCountryAlt = $sCountryAlt;
        $params->sPhoneAlt = $sPhoneAlt;
        $params->sMrMrsBus = $sMrMrsBus;
        $params->sFNameBus = $sFNameBus;
        $params->sMIBus = $sMIBus;
        $params->sLNameBus = $sLNameBus;
        $params->sCompanyBus = $sCompanyBus;
        $params->sAddr1Bus = $sAddr1Bus;
        $params->sAddr2Bus = $sAddr2Bus;
        $params->sCityBus = $sCityBus;
        $params->sRegionBus = $sRegionBus;
        $params->sPostalCodeBus = $sPostalCodeBus;
        $params->sCountryBus = $sCountryBus;
        $params->sPhoneBus = $sPhoneBus;
        $params->sFax = $sFax;
        $params->sEmail = $sEmail;
        $params->sPager = $sPager;
        $params->sMobile = $sMobile;
        $params->bCommercial = $bCommercial;
        $params->bCompanyIsTenant = $bCompanyIsTenant;
        $params->dDOB = $dDOB;
        $params->sTenNote = $sTenNote;
        $params->sLicense = $sLicense;
        $params->sLicRegion = $sLicRegion;
        $params->sSSN = $sSSN;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.98 TenantNewDetailed_v2
     * TenantNewDetailed is used to create a new tenant with all of the information
     * available in the tenant’s
     * table.
     * Note: This does NOT include credit card information. If you wish to store
     * credit card information before
     * moving a tenant in you can store it in the comments section for that tenant.
     */
    public function TenantNewDetailed_v2($sWebPassword, $sMrMrs, $sFName, $sMI, $sLName, $sCompany, $sAddr1, $sAddr2, $sCity, $sRegion, $sPostalCode, $sCountry, $sPhone, $sMrMrsAlt = '', $sFNameAlt = '', $sMIAlt = '', $sLNameAlt = '', $sAddr1Alt = '', $sAddr2Alt = '', $sCityAlt = '', $sRegionAlt = '', $sPostalCodeAlt = '', $sCountryAlt = '', $sPhoneAlt = '', $sMrMrsBus = '', $sFNameBus = '', $sMIBus = '', $sLNameBus = '', $sCompanyBus = '', $sAddr1Bus = '', $sAddr2Bus = '', $sCityBus = '', $sRegionBus = '', $sPostalCodeBus = '', $sCountryBus = '', $sPhoneBus = '', $sFax = '', $sEmail = '', $sPager = '', $sMobile = '', $bCommercial = '', $bCompanyIsTenant = '', $dDOB = '', $sTenNote = '', $sLicense = '', $sLicRegion = '', $sSSN = '', $sGateCode = '')
    {
        $params = new stdClass();
        $params->sWebPassword = $sWebPassword;
        $params->sMrMrs = $sMrMrs;
        $params->sFName = $sFName;
        $params->sMI = $sMI;
        $params->sLName = $sLName;
        $params->sCompany = $sCompany;
        $params->sAddr1 = $sAddr1;
        $params->sAddr2 = $sAddr2;
        $params->sCity = $sCity;
        $params->sRegion = $sRegion;
        $params->sPostalCode = $sPostalCode;
        $params->sCountry = $sCountry;
        $params->sPhone = $sPhone;
        $params->sMrMrsAlt = $sMrMrsAlt;
        $params->sFNameAlt = $sFNameAlt;
        $params->sMIAlt = $sMIAlt;
        $params->sLNameAlt = $sLNameAlt;
        $params->sAddr1Alt = $sAddr1Alt;
        $params->sAddr2Alt = $sAddr2Alt;
        $params->sCityAlt = $sCityAlt;
        $params->sRegionAlt = $sRegionAlt;
        $params->sPostalCodeAlt = $sPostalCodeAlt;
        $params->sCountryAlt = $sCountryAlt;
        $params->sPhoneAlt = $sPhoneAlt;
        $params->sMrMrsBus = $sMrMrsBus;
        $params->sFNameBus = $sFNameBus;
        $params->sMIBus = $sMIBus;
        $params->sLNameBus = $sLNameBus;
        $params->sCompanyBus = $sCompanyBus;
        $params->sAddr1Bus = $sAddr1Bus;
        $params->sAddr2Bus = $sAddr2Bus;
        $params->sCityBus = $sCityBus;
        $params->sRegionBus = $sRegionBus;
        $params->sPostalCodeBus = $sPostalCodeBus;
        $params->sCountryBus = $sCountryBus;
        $params->sPhoneBus = $sPhoneBus;
        $params->sFax = $sFax;
        $params->sEmail = $sEmail;
        $params->sPager = $sPager;
        $params->sMobile = $sMobile;
        $params->bCommercial = $bCommercial;
        $params->bCompanyIsTenant = $bCompanyIsTenant;
        $params->dDOB = $dDOB;
        $params->sTenNote = $sTenNote;
        $params->sLicense = $sLicense;
        $params->sLicRegion = $sLicRegion;
        $params->sSSN = $sSSN;
        $params->sGateCode = $sGateCode;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.99 TenantNoteInsert
     * TenantNoteInsert is used to add a tenant note to a specific tenant’s ledger.
     */
    public function TenantNoteInsert($iLedgerID, $sNote)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        $params->sNote = $sNote;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.100 TenantNotesRetrive
     * TenantNotesRetrive is used to get all notes that are available for a specified
     * ledger ID.
     */
    public function TenantNotesRetrieve($iLedgerID)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.101 TenantPasswordByEmailAddress
     * TenantPasswordByEmailAddress is used to get a tenant’s password based on an
     * email address. This is
     * used mainly for password retrieval by email.
     */
    public function TenantPasswordByEmailAddress($sTenantEmailAddress)
    {
        $params = new stdClass();
        $params->sTenantEmailAddress = $sTenantEmailAddress;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.102 TenantSearchDetailed
     * TenantSearchDetailed is used to get all of the tenants that match the given
     * inputs. If no location code is
     * provided the tenant search will search across all sites for a given corporate
     * code. This method also
     * returns tenants who do not match the search parameters exactly.
     * Example: If you search for “Jo Smi” you will get results like “John Smith”
     * Note: This method will only return a maximum of 50 tenants to avoid
     * transmitting too much data
     */
    public function TenantSearchDetailed($sTenantFirstName, $sTenantLastName, $sAddressLine1, $sAddressLine2, $sCity, $sState, $sZipCode, $sEmailAddress, $sPhoneNumber, $sPhoneNumber2, $sPhoneNumber3, $sPhoneNumber4)
    {
        $params = new stdClass();
        $params->sTenantFirstName = $sTenantFirstName;
        $params->sTenantLastName = $sTenantLastName;
        $params->sAddressLine1 = $sAddressLine1;
        $params->sAddressLine2 = $sAddressLine2;
        $params->sCity = $sCity;
        $params->sState = $sState;
        $params->sZipCode = $sZipCode;
        $params->sEmailAddress = $sEmailAddress;
        $params->sPhoneNumber = $sPhoneNumber;
        $params->sPhoneNumber2 = $sPhoneNumber2;
        $params->sPhoneNumber3 = $sPhoneNumber3;
        $params->sPhoneNumber4 = $sPhoneNumber4;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.103 TenantSMSOptInUpdate
     * TenantSMSOptInUpdate is used to set the tenant flag for accepting to receive
     * SMS messages.
     */
    public function TenantSMSOptInUpdate($iTenantID, $bSMSOptIn)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->bSMSOptIn = $bSMSOptIn;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.104 TenantUpdate
     * TenantUpdate is used to update an existing tenant. You must first call
     * TenantList to find the tenant’s ID
     * which has to be passed in as a parameter. Whatever parameters are passed into
     * this method will
     * overwrite the existing stored information. If you do not wish to update a
     * section you must pass in the
     * original data retrieved when calling TenantInfoByTenantID for that tenant.
     * Note: This does NOT include credit card information. If you wish to store
     * credit card information before
     * moving a tenant in you can store it in the comments section for that tenant.
     */
    public function TenantUpdate($iTenantID, $sGateCode, $sWebPassword, $sMrMrs, $sFName, $sMI, $sLName, $sCompany, $sAddr1, $sAddr2, $sCity, $sRegion, $sPostalCode, $sCountry, $sPhone, $sMrMrsAlt, $sFNameAlt, $sMIAlt, $sLNameAlt, $sAddr1Alt, $sAddr2Alt, $sCityAlt, $sRegionAlt, $sPostalCodeAlt, $sCountryAlt, $sPhoneAlt, $sMrMrsBus, $sFNameBus, $sMIBus, $sLNameBus, $sCompanyBus, $sAddr1Bus, $sAddr2Bus, $sCityBus, $sRegionBus, $sPostalCodeBus, $sCountryBus, $sPhoneBus, $sFax, $sEmail, $sPager, $sMobile, $bCommercial, $bCompanyIsTenant, $dDOB, $sTenNote, $sLicense, $sLicRegion, $sSSN)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->sGateCode = $sGateCode;
        $params->sWebPassword = $sWebPassword;
        $params->sMrMrs = $sMrMrs;
        $params->sFName = $sFName;
        $params->sMI = $sMI;
        $params->sLName = $sLName;
        $params->sCompany = $sCompany;
        $params->sAddr1 = $sAddr1;
        $params->sAddr2 = $sAddr2;
        $params->sCity = $sCity;
        $params->sRegion = $sRegion;
        $params->sPostalCode = $sPostalCode;
        $params->sCountry = $sCountry;
        $params->sPhone = $sPhone;
        $params->sMrMrsAlt = $sMrMrsAlt;
        $params->sFNameAlt = $sFNameAlt;
        $params->sMIAlt = $sMIAlt;
        $params->sLNameAlt = $sLNameAlt;
        $params->sAddr1Alt = $sAddr1Alt;
        $params->sAddr2Alt = $sAddr2Alt;
        $params->sCityAlt = $sCityAlt;
        $params->sRegionAlt = $sRegionAlt;
        $params->sPostalCodeAlt = $sPostalCodeAlt;
        $params->sCountryAlt = $sCountryAlt;
        $params->sPhoneAlt = $sPhoneAlt;
        $params->sMrMrsBus = $sMrMrsBus;
        $params->sFNameBus = $sFNameBus;
        $params->sMIBus = $sMIBus;
        $params->sLNameBus = $sLNameBus;
        $params->sCompanyBus = $sCompanyBus;
        $params->sAddr1Bus = $sAddr1Bus;
        $params->sAddr2Bus = $sAddr2Bus;
        $params->sCityBus = $sCityBus;
        $params->sRegionBus = $sRegionBus;
        $params->sPostalCodeBus = $sPostalCodeBus;
        $params->sCountryBus = $sCountryBus;
        $params->sPhoneBus = $sPhoneBus;
        $params->sFax = $sFax;
        $params->sEmail = $sEmail;
        $params->sPager = $sPager;
        $params->sMobile = $sMobile;
        $params->bCommercial = $bCommercial;
        $params->bCompanyIsTenant = $bCompanyIsTenant;
        $params->dDOB = $dDOB;
        $params->sTenNote = $sTenNote;
        $params->sLicense = $sLicense;
        $params->sLicRegion = $sLicRegion;
        $params->sSSN = $sSSN;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.105 TenantUpdate_v2
     * TenantUpdate_v2 is used to update an existing tenant. Version 2 includes
     * alternate email and
     * relationship. You must first call TenantList to find the tenant’s ID which has
     * to be passed in as a
     * parameter. Whatever parameters are passed into this method will overwrite the
     * existing stored
     * information. If you do not wish to update a section you must pass in the
     * original data retrieved when
     * calling TenantInfoByTenantID for that tenant.
     * Note: This does NOT include credit card information. If you wish to store
     * credit card information before
     * moving a tenant in you can store it in the comments section for that tenant.
     */
    public function TenantUpdate_v2($iTenantID, $sGateCode, $sWebPassword, $sMrMrs, $sFName, $sMI, $sLName, $sCompany, $sAddr1, $sAddr2, $sCity, $sRegion, $sPostalCode, $sCountry, $sPhone, $sMrMrsAlt, $sFNameAlt, $sMIAlt, $sLNameAlt, $sAddr1Alt, $sAddr2Alt, $sCityAlt, $sRegionAlt, $sPostalCodeAlt, $sCountryAlt, $sPhoneAlt, $sMrMrsBus, $sFNameBus, $sMIBus, $sLNameBus, $sCompanyBus, $sAddr1Bus, $sAddr2Bus, $sCityBus, $sRegionBus, $sPostalCodeBus, $sCountryBus, $sPhoneBus, $sFax, $sEmail, $sPager, $sMobile, $bCommercial, $bCompanyIsTenant, $dDOB, $sTenNote, $sLicense, $sLicRegion, $sSSN, $sEmailAlt, $sRelationshipAlt)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->sGateCode = $sGateCode;
        $params->sWebPassword = $sWebPassword;
        $params->sMrMrs = $sMrMrs;
        $params->sFName = $sFName;
        $params->sMI = $sMI;
        $params->sLName = $sLName;
        $params->sCompany = $sCompany;
        $params->sAddr1 = $sAddr1;
        $params->sAddr2 = $sAddr2;
        $params->sCity = $sCity;
        $params->sRegion = $sRegion;
        $params->sPostalCode = $sPostalCode;
        $params->sCountry = $sCountry;
        $params->sPhone = $sPhone;
        $params->sMrMrsAlt = $sMrMrsAlt;
        $params->sFNameAlt = $sFNameAlt;
        $params->sMIAlt = $sMIAlt;
        $params->sLNameAlt = $sLNameAlt;
        $params->sAddr1Alt = $sAddr1Alt;
        $params->sAddr2Alt = $sAddr2Alt;
        $params->sCityAlt = $sCityAlt;
        $params->sRegionAlt = $sRegionAlt;
        $params->sPostalCodeAlt = $sPostalCodeAlt;
        $params->sCountryAlt = $sCountryAlt;
        $params->sPhoneAlt = $sPhoneAlt;
        $params->sMrMrsBus = $sMrMrsBus;
        $params->sFNameBus = $sFNameBus;
        $params->sMIBus = $sMIBus;
        $params->sLNameBus = $sLNameBus;
        $params->sCompanyBus = $sCompanyBus;
        $params->sAddr1Bus = $sAddr1Bus;
        $params->sAddr2Bus = $sAddr2Bus;
        $params->sCityBus = $sCityBus;
        $params->sRegionBus = $sRegionBus;
        $params->sPostalCodeBus = $sPostalCodeBus;
        $params->sCountryBus = $sCountryBus;
        $params->sPhoneBus = $sPhoneBus;
        $params->sFax = $sFax;
        $params->sEmail = $sEmail;
        $params->sPager = $sPager;
        $params->sMobile = $sMobile;
        $params->bCommercial = $bCommercial;
        $params->bCompanyIsTenant = $bCompanyIsTenant;
        $params->dDOB = $dDOB;
        $params->sTenNote = $sTenNote;
        $params->sLicense = $sLicense;
        $params->sLicRegion = $sLicRegion;
        $params->sSSN = $sSSN;
        $params->sEmailAlt = $sEmailAlt;
        $params->sRelationshipAlt = $sRelationshipAlt;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.106 TenantUpdate_NationalAccount
     * TenantUpdate_NationalAccount is used to update the national master and national
     * franchise account
     * numbers for a specified tenant. If a value is passed in that is less than 0
     * then the existing number will be
     * used. If the value passed in equals 0 then we will delete the existing number
     * and set it to NULL. If the
     * number is greater than zero then we will update the stored value.
     */
    public function TenantUpdate_NationalAccount($iTenantID, $iNationalMasterAcct, $iNationalFranchiseAcct)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iNationalMasterAcct = $iNationalMasterAcct;
        $params->iNationalFranchiseAcct = $iNationalFranchiseAcct;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.107 TimeZonesRetrieve
     * TimeZonesRetrieve is used to get a list of all time zones for a specified
     * location. These are used in
     * conjuncture with MoveInWithDiscount_v4 function.
     */
    public function TimeZonesRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.108 UnitContentsRetrieve
     * UnitContentsRetrieve is used to retrieve the current contents of a unit based
     * on a specific tenant’s
     * ledger.
     */
    public function UnitContentsRetrieve($iLedgerID)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.109 UnitContentsUpdate
     * UnitContentsUpdate is used to update the current contents of a unit based on a
     * specific tenant’s ledger.
     */
    public function UnitContentsUpdate($iLedgerID, $sNote)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        $params->sNote = $sNote;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.112 UnitsInformationAvailableUnitsOnly_v2
     * UnitsInformationAvailableUnitsOnly_v2 is used to retrieve all of the unit
     * information for available units
     * only based on the last time unit information was polled. In order to reduce
     * bandwidth overhead and
     * ensure that your website or call center is running optimally you should have a
     * local unit cache on your
     * server that refreshes the unit information approximately every 15 minutes.
     * Note: A unit that is reserved on the waiting list is still marked as available.
     */
    public function UnitsInformationAvailableUnitsOnly_v2($lngLastTimePolled)
    {
        $params = new stdClass();
        $params->lngLastTimePolled = $lngLastTimePolled;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.113 UnitsInformationByUnitName
     * UnitsInformationByUnitName is used to retrieve all of the unit information for
     * a specific unit with matching
     * unit name.
     */
    public function UnitsInformationByUnitName($sUnitName)
    {
        $params = new stdClass();
        $params->sUnitName = $sUnitName;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.114 UnitsInformation_v2
     * UnitsInformation_v2 is used to retrieve all of the unit information for a
     * particular site based on the last
     * time unit information was polled. In order to reduce bandwidth overhead and
     * ensure that your website or
     * call center is running optimally you should have a local unit cache on your
     * server that refreshes the unit
     * information approximately every 15 minutes.
     */
    public function UnitsInformation_v2($lngLastTimePolled)
    {
        $params = new stdClass();
        $params->lngLastTimePolled = $lngLastTimePolled;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.114 UnitsInformation_v3
     * UnitsInformation_v3 is used to retrieve all of the unit information for a
     * particular site based on the last
     * time unit information was polled. In order to reduce bandwidth overhead and
     * ensure that your website or
     * call center is running optimally you should have a local unit cache on your
     * server that refreshes the unit
     * information approximately every 15 minutes.
     */
    public function UnitsInformation_v3($lngLastTimePolled,$bReturnExcludedFromWebsiteUnits)
    {
        $params = new stdClass();
        $params->lngLastTimePolled = $lngLastTimePolled;
        $params->bReturnExcludedFromWebsiteUnits=$bReturnExcludedFromWebsiteUnits;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.115 UnitTypePriceList
     * UnitTypePriceList is used to generate a price list for all unit types. The list
     * will include all standard
     * information about the unit type as well as display the first available unit for
     * each type.
     */
    public function UnitTypePriceList()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * 7.116 UnitTypePriceList_v2
     * UnitTypePriceList_v2 is used to generate a price list for all unit types. The
     * list will include all standard
     * information about the unit type as well as display the first available unit for
     * each type. This version also
     * returns the amount required for admin fee, reservation fee, and whether or not
     * the unit type is inside.
     */
    public function UnitTypePriceList_v2()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * ScheduledAuctions is used to retrieve the current auctions.
     */
    public function ScheduledAuctions()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    public function CorpUserList($sUsagePassword)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        return $this->_call(__FUNCTION__, $params);
    }

    public function UnitStandardRateUpdate($sUsagePassword, $sUnitIDsCommaDelimited, $dcStdRate)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        $params->sUnitIDsCommaDelimited = $sUnitIDsCommaDelimited;
        $params->dcStdRate = $dcStdRate;
        return $this->_call(__FUNCTION__, $params);
    }

    public function UnitPushRateUpdate($sUsagePassword, $sUnitIDsCommaDelimited, $dcStdRate)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        $params->sUnitIDsCommaDelimited = $sUnitIDsCommaDelimited;
        $params->dcStdRate = $dcStdRate;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * @param $sUsagePassword
     * @param $sUnitIDsCommaDelimited
     * @param $dcPushRate
     * @param $iRatesTaxInclusive
     * @return mixed
     */
    public function UnitPushRateUpdate_v2($sUsagePassword, $sUnitIDsCommaDelimited, $dcPushRate, $iRatesTaxInclusive){
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        $params->sUnitIDsCommaDelimited = $sUnitIDsCommaDelimited;
        $params->dcPushRate = $dcPushRate;
        $params->iRatesTaxInclusive = $iRatesTaxInclusive;
        return $this->_call(__FUNCTION__, $params);
    }

    /**
     * @param $sUsagePassword
     * @param $sUnitIDsCommaDelimited
     * @param $dcStdMonthlyRate
     * @param $dcStdWeeklyRate
     * @param $iRatesTaxInclusive
     * @return mixed
     */
    public function UnitStandardRateUpdate_v3($sUsagePassword, $sUnitIDsCommaDelimited, $dcStdMonthlyRate, $dcStdWeeklyRate, $iRatesTaxInclusive)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        $params->sUnitIDsCommaDelimited = $sUnitIDsCommaDelimited;
        $params->dcStdMonthlyRate = $dcStdMonthlyRate;
        $params->dcStdWeeklyRate = $dcStdWeeklyRate;
        $params->iRatesTaxInclusive = $iRatesTaxInclusive;
        return $this->_call(__FUNCTION__, $params);
    }

    public function MoveInCostRetrieveWithDiscount_Reservation($iUnitID, $dMoveInDate, $InsuranceCoverageID, $ConcessionPlanID, $WaitingID)
    {
        $params = new stdClass();
        $params->iUnitID = $iUnitID;
        $params->dMoveInDate = $dMoveInDate;
        $params->InsuranceCoverageID = $InsuranceCoverageID;
        $params->ConcessionPlanID = $ConcessionPlanID;
        $params->WaitingID = $WaitingID;
        return $this->_call(__FUNCTION__, $params);
    }

    public function MoveOut($sUsagePassword, $TenantID, $UnitID)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        $params->TenantID = $TenantID;
        $params->UnitID = $UnitID;
        return $this->_call(__FUNCTION__, $params);
    }

    public function TenantImagePathUpdate($iTenantID, $iPictureNum, $sFullImagePath, $bSetPrimaryPic)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iPictureNum = $iPictureNum;
        $params->sFullImagePath = $sFullImagePath;
        $params->bSetPrimaryPic = $bSetPrimaryPic;
        return $this->_call(__FUNCTION__, $params);
    }

    public function TenantImagePathRetrieve($iTenantID, $iPictureNum)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iPictureNum = $iPictureNum;
        return $this->_call(__FUNCTION__, $params);
    }

    public function MapShapesRetrieve()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    public function ReservationList_v2($iGlobalWaitingNum, $WaitingID)
    {
        $params = new stdClass();
        $params->iGlobalWaitingNum = $iGlobalWaitingNum;
        $params->WaitingID = $WaitingID;
        return $this->_call(__FUNCTION__, $params);
    }

    public function TenantConnectSettingsRetrieve($sUsagePassword)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        return $this->_call(__FUNCTION__, $params);
    }

    public function TenantConnectSettingsUpdate($iTenantConnectEnabled, $sUsagePassword)
    {
        $params = new stdClass();
        $params->iTenantConnectEnabled = $iTenantConnectEnabled;
        $params->sUsagePassword = $sUsagePassword;
        return $this->_call(__FUNCTION__, $params);
    }

    public function MoveInOutList($sUsagePassword)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        return $this->_call(__FUNCTION__, $params);
    }

    public function LedgerInvoiceUpdate($LedgerID, $bInvoice)
    {
        $params = new stdClass();
        $params->LedgerID = $LedgerID;
        $params->bInvoice = $bInvoice;
        return $this->_call(__FUNCTION__, $params);
    }

    public function PaymentSimpleWithPrepaidDiscount($iTenantID, $iUnitID, $dcPaymentAmount, $iPaymentMethod, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $sABARoutingNum, $sAccountNum, $iAccountType, $iSource, $iConcessionID, $bTestMode)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iPaymentMethod = $iPaymentMethod;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->sABARoutingNum = $sABARoutingNum;
        $params->sAccountNum = $sAccountNum;
        $params->iAccountType = $iAccountType;
        $params->iSource = $iSource;
        $params->iConcessionID = $iConcessionID;
        $params->bTestMode = $bTestMode;
        return $this->_call(__FUNCTION__, $params);
    }

    public function CustomerAccountsBalanceDetailsWithDiscount($iTenantID, $iUnitID, $iConcessionID)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->iConcessionID = $iConcessionID;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function CallStoredProcedure($QRID, $procName)
    {
        $params = new stdClass();
        $params->QRID = $QRID;
        $params->procName = $procName;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function CustomBillingDateCharges($sUsagePassword, $dBillingDate)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        $params->dBillingDate = $dBillingDate;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10
     * CustomerAccountsChargesWithPrepayment is used to retrieve a tenants current
     * charges and payments.
     * This is the same set of charges that gets returned with
     * CustomerAccountsBalanceDetailsWithPrepayment,
     * however, it is not grouped by charge category. This web service allows an input
     * value for the
     * number of months to prepay.*/

    public function CustomerAccountsChargesWithPrepayment($iTenantID, $iNumberOfMonthsPrepay)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iNumberOfMonthsPrepay = $iNumberOfMonthsPrepay;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function DiscountPlanUnitTypesList($sConcessionIDs)
    {
        $params = new stdClass();
        $params->sConcessionIDs = $sConcessionIDs;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function DiscountPlanUpdate($sConcessionIDs, $iShowOn, $dcMaxOccPct, $sPlanStrt, $sPlanEnd, $iAvailableAt, $iDisabled, $iExcludeIfLessThanUnitsTotal, $iExcludeIfMoreThanUnitsTotal, $dcMaxOccPctExcludeIfMoreThanUnitsTotal, $sConcessionUnitTypeIDs, $iConcessionUnitTypeOverwrite)
    {
        $params = new stdClass();
        $params->sConcessionIDs = $sConcessionIDs;
        $params->iShowOn = $iShowOn;
        $params->dcMaxOccPct = $dcMaxOccPct;
        $params->sPlanStrt = $sPlanStrt;
        $params->sPlanEnd = $sPlanEnd;
        $params->iAvailableAt = $iAvailableAt;
        $params->iDisabled = $iDisabled;
        $params->iExcludeIfLessThanUnitsTotal = $iExcludeIfLessThanUnitsTotal;
        $params->iExcludeIfMoreThanUnitsTotal = $iExcludeIfMoreThanUnitsTotal;
        $params->dcMaxOccPctExcludeIfMoreThanUnitsTotal = $dcMaxOccPctExcludeIfMoreThanUnitsTotal;
        $params->sConcessionUnitTypeIDs = $sConcessionUnitTypeIDs;
        $params->iConcessionUnitTypeOverwrite = $iConcessionUnitTypeOverwrite;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function DiscountPlanUpdateSimple($sConcessionIDs, $iDisabled, $sConcessionUnitTypeIDs, $iConcessionUnitTypeOverwrite)
    {
        $params = new stdClass();
        $params->sConcessionIDs = $sConcessionIDs;
        $params->iDisabled = $iDisabled;
        $params->sConcessionUnitTypeIDs = $sConcessionUnitTypeIDs;
        $params->iConcessionUnitTypeOverwrite = $iConcessionUnitTypeOverwrite;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function EmployeeLoginCCC($sUsagePassword)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015
     * This method will allow updating the billing date (or anniversary date) of a
     * ledger account.
     *
     * The Billing Day will be new due day for all future rent and monthly charges.
     * This will be a
     * value between 1 and 31. 31 should be used if they always want billing on the
     * last day of the month.
     *
     * The method will make the necessary adjustment to the tenant’s ledger adding the
     * prorated bridging
     * charges to bring the account in sync with the new dates. This will always add a
     * Rent charge, and
     * depending upon the account it may also add any additional recurring charges or
     * insurance charges
     * necessary to keep everything in sync. The appropriate notes will be added to
     * the tenants account and
     * an exception will be logged. Everything that would normally be done within the
     * SiteLink client.
     *
     * The method will return a table of the charges for the ledger. This would
     * include any outstanding
     * charges and their balance as well as the newly added bridging charges. The
     * charges table can be
     * used to determine what the balance owed would be after making the ledger
     * changes as well as what the
     * future paid thru date would be. You would be able to filter on the “bNewChg”
     * flag to determine the
     * total charge amount that was created by the call.*/

    public function LedgerBillingDayUpdate($iLedgerID, $iBillingDay, $UpdateFlag)
    {
        $params = new stdClass();
        $params->iLedgerID = $iLedgerID;
        $params->iBillingDay = $iBillingDay;
        $params->UpdateFlag = $UpdateFlag;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function PaymentSimpleWithSource_v2($iTenantID, $iUnitID, $dcPaymentAmount, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $sCCTrack2, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $bTestMode, $iSource)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iUnitID = $iUnitID;
        $params->dcPaymentAmount = $dcPaymentAmount;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->sCCTrack2 = $sCCTrack2;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->bTestMode = $bTestMode;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function PhoneIntegrationPushCallInformation($sUsagePassword, $iCallTrackingProvider, $sIdentifier, $sCallData)
    {
        $params = new stdClass();
        $params->sUsagePassword = $sUsagePassword;
        $params->iCallTrackingProvider = $iCallTrackingProvider;
        $params->sIdentifier = $sIdentifier;
        $params->sCallData = $sCallData;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function PostalCodeOwnerMarketsList()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function ReservationFeeAddWithSourceForMobileStorage($iTenantID, $iQTRentalId, $iCreditCardType, $sCreditCardNumber, $sCreditCardCVV, $dExpirationDate, $sBillingName, $sBillingAddress, $sBillingZipCode, $bTestMode, $iSource)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iQTRentalId = $iQTRentalId;
        $params->iCreditCardType = $iCreditCardType;
        $params->sCreditCardNumber = $sCreditCardNumber;
        $params->sCreditCardCVV = $sCreditCardCVV;
        $params->dExpirationDate = $dExpirationDate;
        $params->sBillingName = $sBillingName;
        $params->sBillingAddress = $sBillingAddress;
        $params->sBillingZipCode = $sBillingZipCode;
        $params->bTestMode = $bTestMode;
        $params->iSource = $iSource;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10
     * ReservationNewWithSource_v5 is used to create a new reservation for a tenant
     * with the source and
     * type of inquiry included for tracking purposes. This method also allows the
     * quoted rate to be set,
     * the date at which the reservation expires, the date to follow up on the
     * reservation, a tracking code,
     * a caller ID number and a concession ID. The Concession ID will be used to show
     * a reservation that
     * is requested to be given to the tenant at move in. Note that web only
     * concessions will still work
     * when moved in via the SiteLink client.
     *
     * The tenant either has to be in the system already or has to be created first
     * before calling this method.
     * When this method is called the tenant will be placed directly on the Waiting
     * List in SiteLink and will
     * NOT be put on the bulletin board.*/

    public function ReservationNewWithSource_v5($sTenantID, $sUnitID, $dNeeded, $sComment, $iSource, $sSource, $QTRentalTypeID, $iInquiryType, $dcQuotedRate, $dExpires, $dFollowUp, $sTrackingCode, $sCallerID, $ConcessionID)
    {
        $params = new stdClass();
        $params->sTenantID = $sTenantID;
        $params->sUnitID = $sUnitID;
        $params->dNeeded = $dNeeded;
        $params->sComment = $sComment;
        $params->iSource = $iSource;
        $params->sSource = $sSource;
        $params->QTRentalTypeID = $QTRentalTypeID;
        $params->iInquiryType = $iInquiryType;
        $params->dcQuotedRate = $dcQuotedRate;
        $params->dExpires = $dExpires;
        $params->dFollowUp = $dFollowUp;
        $params->sTrackingCode = $sTrackingCode;
        $params->sCallerID = $sCallerID;
        $params->ConcessionID = $ConcessionID;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10 */
    public function SiteLinkeSignCreateLeaseURL($iTenantID, $iLedgerID, $sReturnUrl)
    {
        $params = new stdClass();
        $params->iTenantID = $iTenantID;
        $params->iLedgerID = $iLedgerID;
        $params->sReturnUrl = $sReturnUrl;
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10
     * [Deprecated]
     * (Requires API Regular or API Aggregator)
     * UnitsInformation is used to retrieve all of the unit information for a
     * particular site.*/

    public function UnitsInformation()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10
     * [Deprecated]
     * (Requires API Regular or API Aggregator)
     * UnitsInformationAvailableUnitsOnly is used to retrieve all of the unit
     * information
     * for*/

    public function UnitsInformationAvailableUnitsOnly()
    {
        $params = new stdClass();
        return $this->_call(__FUNCTION__, $params);
    }

    /* added March, 16 2015, api v2.10
     * 9.118 UnitsInformationByUnitID
     * (Requires API Regular or API Aggregator)
     * UnitsInformationByUnitID is used to retrieve all of the unit information
     * for a specific unit with matching unit ID.*/
    public function UnitsInformationByUnitID($UnitID)
    {
        $params = new stdClass();
        $params->UnitID = $UnitID;
        return $this->_call(__FUNCTION__, $params);
    }
    /* 9.35 UnitStatusUpdate
     * UnitStatusUpdate is used to change the bRentable, bNeedsService and sUnitNote
     * for a specific unit with matching unit ID.*/
    public function UnitStatusUpdate($UnitID,$bRentable,$bNeedsService,$sUnitNote)
    {
        $params = new stdClass();
        $params->UnitID = $UnitID;
        $params->bRentable = $bRentable;
        $params->bNeedsService = $bNeedsService;
        $params->sUnitNote = $sUnitNote;
        return $this->_call(__FUNCTION__, $params);
    }
    
    /* 9.36 UnitAdd
     * UnitAdd is used to Add new units */
    public function UnitAdd($sUnitName,$UnitTypeID,$dcWidth,$dcLength,$dcStdRate,$dcStdWeeklyRate,$dcStdSecDep,$dcStdLateFee,$iFloor,$bPower,$bClimate,$bInside,$bAlarm,$bCollapsible,$bMobile,$bCorporate,$iEntryLoc,$iDoorType,$bRentable,$iADA,$bExcludeFromWebsite)
    {
        $params = new stdClass();
        $params->sUnitName = $sUnitName;
        $params->UnitTypeID = $UnitTypeID;
        $params->dcWidth = $dcWidth;
        $params->dcLength = $dcLength;
        $params->dcStdRate = $dcStdRate;
        $params->dcStdWeeklyRate = $dcStdWeeklyRate;
        $params->dcStdSecDep = $dcStdSecDep;
        $params->dcStdLateFee = $dcStdLateFee;
        $params->iFloor = $iFloor;
        $params->bPower = $bPower;
        $params->bClimate = $bClimate;
        $params->bInside = $bInside;
        $params->bAlarm = $bAlarm;
        $params->bCollapsible = $bCollapsible;
        $params->bMobile = $bMobile;
        $params->bCorporate = $bCorporate;
        $params->iEntryLoc = $iEntryLoc;
        $params->iDoorType = $iDoorType;
        $params->bRentable = $bRentable;
        $params->iADA = $iADA;
        $params->bExcludeFromWebsite = $bExcludeFromWebsite;
        return $this->_call(__FUNCTION__, $params);
    }
    
    /* 9.37 UnitDelete
     * UnitDelete is used to Delete Units
     * for a specific unit with matching unit ID.*/
    public function UnitDelete($UnitID)
    {
        $params = new stdClass();
        $params->UnitID = $UnitID;
        return $this->_call(__FUNCTION__, $params);
    }
}

?>
