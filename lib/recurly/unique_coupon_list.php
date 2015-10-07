<?php

class Recurly_UniqueCouponList extends Recurly_Pager
{
  public static function get($params = null, $client = null) {
    $list = new Recurly_UniqueCouponList(Recurly_Client::PATH_UNIQUE_COUPONS, $client);
    $list->_loadFrom(Recurly_Client::PATH_UNIQUE_COUPONS, $params);
    return $list;
  }

  protected function getNodeName() {
    return 'unique_coupon_codes';
  }
}
