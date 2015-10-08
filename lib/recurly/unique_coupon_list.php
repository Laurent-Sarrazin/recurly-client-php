<?php

class Recurly_UniqueCouponList extends Recurly_Pager
{
  public static function createFromUri($uri, $client) {
    $path = str_replace('/v2', '', parse_url($uri, PHP_URL_PATH));
    $query = parse_url($uri, PHP_URL_QUERY);
    $params = parse_str($query);

    $list = new Recurly_UniqueCouponList(null, $client);
    $list->_loadFrom($path, $params);

    return $list;
  }

  protected function getNodeName() {
    return 'unique_coupon_codes';
  }
}
