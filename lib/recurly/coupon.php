<?php

class Recurly_Coupon extends Recurly_Resource
{
  protected static $_writeableAttributes;
  protected static $_nestedAttributes;
  protected $_redeemUrl;

  function __construct() {
    parent::__construct();
    $this->discount_in_cents = new Recurly_CurrencyList('discount_in_cents');
  }

  public static function init()
  {
    Recurly_Coupon::$_writeableAttributes = array(
      'coupon_code','name','discount_type','redeem_by_date','single_use','applies_for_months',
      'duration', 'temporal_unit', 'temporal_amount',
      'max_redemptions','applies_to_all_plans','discount_percent','discount_in_cents','plan_codes',
      'hosted_description','invoice_description', 'applies_to_non_plan_charges', 'redemption_resource',
      'max_redemptions_per_account', 'coupon_type', 'unique_code_template', 'unique_coupon_codes'
    );
    Recurly_Coupon::$_nestedAttributes = array();
  }

  public static function get($couponCode, $client = null) {
    return Recurly_Base::_get(Recurly_Coupon::uriForCoupon($couponCode), $client);
  }

  public function create() {
    $this->_save(Recurly_Client::POST, Recurly_Client::PATH_COUPONS);
  }

  public function redeemCoupon($accountCode, $currency, $subscriptionUUID = null) {
    if ($this->state != 'redeemable') {
      throw new Recurly_Error('Coupon is not redeemable.');
    }

    $redemption = new Recurly_CouponRedemption(null, $this->_client);
    $redemption->account_code = $accountCode;
    $redemption->currency = $currency;
    $redemption->subscription_uuid = $subscriptionUUID;

    foreach ($this->_links as $link) {
      if ($link->name == 'redeem') {
        $redemption->_save(strtoupper($link->method), $link->href);
        return $redemption;
      }
    }
  }

  public function delete() {
    return Recurly_Base::_delete($this->uri(), $this->_client);
  }
  public static function deleteCoupon($couponCode, $client = null) {
    return Recurly_Base::_delete(Recurly_Coupon::uriForCoupon($couponCode), $client);
  }

  public function generate($amount) {
    $doc = $this->createDocument();

    $root = $doc->appendChild($doc->createElement($this->getNodeName()));
    $root->appendChild($doc->createElement('number_of_unique_codes', $amount));

    $response = $this->_client->request(Recurly_Client::POST, $this->uri() . '/generate', $this->renderXML($doc));
    $response->assertValidResponse();

    return Recurly_UniqueCouponList::createFromUri($response->headers['Location'], $this->_client);
  }

  protected function uri() {
    if (!empty($this->_href))
      return $this->getHref();
    else
      return Recurly_Coupon::uriForCoupon($this->coupon_code);
  }
  protected static function uriForCoupon($couponCode) {
    return Recurly_Client::PATH_COUPONS . '/' . rawurlencode($couponCode);
  }

  protected function getNodeName() {
    return 'coupon';
  }
  protected function getWriteableAttributes() {
    return Recurly_Coupon::$_writeableAttributes;
  }
  protected function getRequiredAttributes() {
    return array();
  }
}

Recurly_Coupon::init();
