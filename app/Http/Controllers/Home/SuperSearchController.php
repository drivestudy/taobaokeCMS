<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Home\BaseController;
use App\Libraries\Alimama\Contracts\AlimamaInterface;
use App\Traits\TpwdParameter;
use App\Traits\EncryptOrDecryptImage;
use App\Traits\ShowFromToView;
use App\Models\CouponCategory;
use App\Models\Coupon;
use App\Models\Category;

class SuperSearchController extends BaseController
{
    use TpwdParameter, ShowFromToView, EncryptOrDecryptImage;

    public $taobao;

    public function __construct(Request $request, AlimamaInterface $taobao)
    {
      $this->taobao = $taobao;
      $this->__construct_base($request);
    }

    // 超级搜索
    public function index (Request $request)
    {
      $TDK = ['title'=>'淘宝天猫优惠券查询 | '.config('website.name'),
              'keywords'=>'',
              'description'=>''];
      $show_from = $this->showFrom(self::$from);

      if (self::$from == 'pc') {
        $categorys = Category::categorys(self::$from);
        $currentUrl = $request->url();

        return view('home.pc.superSearch.index', compact('TDK',
                                                         'show_from',
                                                         'categorys',
                                                         'currentUrl'
                                                       ));
      } else {
        $couponsGussYouLike = Coupon::couponsRecommendRandom(self::$from, 5, 4);
        if ($show_from) {
          return view('home.wx.superSearch.index_simple', compact('TDK',
                                                                  'show_from',
                                                                  'couponsGussYouLike'
                                                                ));
        } else {
          $categorys = Category::categorys(self::$from);
          $couponCategorys = CouponCategory::couponCategorys(self::$from);
          return view('home.wx.superSearch.index', compact('TDK',
                                                           'show_from',
                                                           'couponsGussYouLike',
                                                           'categorys',
                                                           'couponCategorys'
                                                         ));
        }
      }
    }

    // 执行搜索-无线端
    public function resultWX (Request $request)
    {
      if (empty($request->q)) {
        return back();
      }

      $TDK = ['title'=>'超级搜索的优惠券商品搜索结果 | '.config('website.name'),
              'keywords'=>'',
              'description'=>''];
      $has_search = true;
      $show_from = $this->showFrom(self::$from);
      $keyword = $this->getKeywordFromSearch($request->q);

      if (self::$from == 'pc' && false) {
        //
      } else {
          $itemCoupons = $this->taobao->tbkDgItemCouponGet(['q'=>$keyword, 'page_size'=>config('alimama.superSearchPageSize')]);
          $itemCouponsArr = $this->getItemCoupons($itemCoupons->results);

          if (count($itemCouponsArr) == 0) {
            return back()->withErrors(['使出了吃奶的力气也没有找到要相关的宝贝，建议搜索其他的宝贝试试或者联系客服进行内部专属渠道人工查询~~~']);
          }

          $couponsGussYouLike = Coupon::couponsRecommendRandom(self::$from, 5, 4);
          $itemCouponsArr = $this->addTaoKouLing($itemCouponsArr);
          if ($show_from) {
            $itemCouponsArr = $this->addImageEncrypt($itemCouponsArr);
            return view('home.wx.superSearch.index_simple', compact('TDK', 'show_from', 'itemCouponsArr', 'has_search', 'couponsGussYouLike'));
          } else {
            $categorys = Category::categorys(self::$from);
            $couponCategorys = CouponCategory::couponCategorys(self::$from);
            return view('home.wx.superSearch.index', compact('TDK', 'show_from', 'itemCouponsArr', 'has_search', 'couponsGussYouLike', 'categorys', 'couponCategorys'));
          }
      }
    }

    // 执行搜索-PC端
    public function resultPC (Request $request)
    {
      if (empty($request->search)) {
        return back();
      }

      $pageSize = '10';
      $keyword = $this->getKeywordFromSearch($request->search);
      $TDK = ['title'=>'"'.$keyword.'"超级搜索的优惠券商品搜索结果 | '.config('website.name'),
              'keywords'=>'',
              'description'=>''];

      $info = [
                'q'=>$keyword,
                'page_size'=>$pageSize,
                'platform'=>1
              ];
      if (!empty($request->page) && $request->page > 1) {
        $info['page_no'] = $request->page;
      } else {
        $info['page_no'] = 1;
      }

      $itemCoupons = $this->taobao->tbkDgItemCouponGet($info);
      $itemCouponsArr = $this->getItemCoupons($itemCoupons->results);
      $itemCouponsAll = $this->taobao->tbkDgItemCouponGet(['q'=>$keyword, 'page_size'=>'10000']);
      $itemCouponsAllArr = $this->getItemCoupons($itemCouponsAll->results);
      $couponsRecommend = Coupon::couponsRecommendRandom(self::$from, 16);
      $categorys = Category::categorys(self::$from);
      $oldRequest = $request->all();
      $currentUrl = $request->url();
      $paginaton['page'] = empty($request->page) ? 1 :$request->page;
      $paginaton['page_size'] = $pageSize;
      $paginaton['count'] = count($itemCouponsAllArr);

      return view('home.pc.superSearch.result_taobao', compact(
                                                           'oldRequest',
                                                           'currentUrl',
                                                           'TDK',
                                                           'itemCouponsArr',
                                                           'paginaton',
                                                           'categorys',
                                                           'couponCategorys',
                                                           'couponsRecommend'
                                                         ));
    }

    // 聚划算的搜索结果-PC端
    public function resultJuPC (Request $request)
    {
      if (empty($request->search)) {
        return back();
      }

      $pageSize = '10';
      $keyword = $this->getKeywordFromSearch($request->search);
      $TDK = ['title'=>'"'.$keyword.'"聚划算的优惠商品搜索结果 | '.config('website.name'),
              'keywords'=>'',
              'description'=>''];

      $info = [
                'word'=>$keyword,
                'page_size'=>$pageSize,
              ];
      if (!empty($request->page) && $request->page > 1) {
        $info['current_page'] = $request->page;
      } else {
        $info['current_page'] = 1;
      }

      $itemsJu = $this->taobao->juItemsSearch($info);
      $itemCouponsArr = $this->getItemJU($itemsJu);
      $couponsRecommend = Coupon::couponsRecommendRandom(self::$from, 16);
      $categorys = Category::categorys(self::$from);
      $oldRequest = $request->all();
      $currentUrl = $request->url();
      $paginaton['page'] = empty($request->page) ? 1 :$request->page;
      $paginaton['page_size'] = $pageSize;
      $paginaton['count'] = empty($itemCouponsArr['total_item']) ? 0 : $itemCouponsArr['total_item'];

      return view('home.pc.superSearch.result_juhuasuan', compact(
                                                           'oldRequest',
                                                           'currentUrl',
                                                           'TDK',
                                                           'itemCouponsArr',
                                                           'paginaton',
                                                           'categorys',
                                                           'couponCategorys',
                                                           'couponsRecommend'
                                                         ));
    }

    // 获取查询的关键词
    public function getKeywordFromSearch ($q)
    {
      if ($this->hasTwpd($q)) {
        $goodsInfoJson = $this->getCouponInfoFromTpwd($q);

        if ((bool)$goodsInfoJson->suc && !empty($goodsInfoJson->content)) {
          $keyword = $this->getQueryKeyWordFromTwpdInfo($goodsInfoJson);
        } else {
          $keyword = $this->guessGoodsNameFromStr($q);
        }
      }

      empty($keyword) ? $keyword = $q : '';

      return $keyword;
    }

    // 处理好券清单的查询词
    public function getQueryKeyWordFromTwpdInfo ($goodsInfoJson)
    {
      $goodsInfo = ((array)$goodsInfoJson);
      $str = str_replace(PHP_EOL, '', $goodsInfo['content']);
      $q = $this->removeTextPrefix($str);
      $q = $this->filterTBApp($q);
      $q = $this->filterTBLMApp($q);
      unset($str);
      unset($goodsInfo);
      unset($goodsInfoJson);
      return $q;
    }

    // 过滤淘宝APP分享的字符串中（）字符来获取商品名称
    public function filterTBApp ($str)
    {
      $q = explode('买的宝贝（', $str);
      if (count($q) == 2) {
        $q = explode('），快来', $q[1]);
      }
      return $q[0];
    }

    // 过滤淘宝联盟APP默认信息的算法获取商品名称
    public function filterTBLMApp ($str) {
      $q = explode('【包邮】', $str);
      unset($str);

      return $q[0];
    }

    // 通过淘宝口令获取口令背后的信息
    public function getCouponInfoFromTpwd ($str)
    {
      return $this->taobao->wirelessShareTpwdQuery($str);
    }

    // 检验字符串中是否存在口令
    public function hasTwpd ($str)
    {
      $strArr = $this->makeTwpdStrToArray($str);

      if (count($strArr) == 3 && strlen($strArr[1]) == 11) {
        return true;
      } else {
        return false;
      }
    }

    // 获取含有淘口令字符串中可能的商品名称
    public function guessGoodsNameFromStr ($str)
    {
      $strArr = $this->makeTwpdStrToArray($str);

      $firstLen = strlen($strArr[0]);
      if ($firstLen > 75 && $firstLen <= 90) {
        return $strArr[0];
      }

      $secondLen = strlen($strArr[0]);
      if ($secondLen > 75 && $secondLen <= 90) {
        return $strArr[2];
      }

      return null;
    }

    // 将含有淘口令的字符串变成数组
    public function makeTwpdStrToArray ($str) {
      $codes = config('alimama.tpwdCode');

      foreach ($codes as  $code) {
        $strArr = explode($code, $str);
        if (count($strArr) == 3) {
          return $strArr;
        }
      }

      return [];
    }

    // 将查询的结果信息转变成数组
    public function getItemCoupons ($json)
    {
      if (empty($json)) {
        return [];
      }

      $array = (array)$json;

      if (empty($array['tbk_coupon']->category)) {
        foreach ($array['tbk_coupon'] as $key => $value) {
          $itemCoupons[$key] = (array)$value;
        }
      } else {
        $oneCoupon = (array)$array['tbk_coupon'];
        $itemCoupons[0] = $oneCoupon;
      }
      unset($array);

      return $itemCoupons;
    }

    // 将聚划算的查询结果转变成数组
    public function getItemJU ($items)
    {
      $result = $items->result;
      $itemsArr = [];
      $itemsArr['current_page'] = empty($result->current_page) ? '': (int)$result->current_page;
      $itemsArr['page_size']    = empty($result->page_size)    ? '': (int)$result->page_size;
      $itemsArr['success']      = empty($result->success)      ? '': (bool)$result->success;
      $itemsArr['total_item']   = empty($result->total_item)   ? '': (int)$result->total_item;
      $itemsArr['total_page']   = empty($result->total_page)   ? '': (int)$result->total_page;
      $itemsArr['total_page']   = empty($result->total_page)   ? '': (int)$result->total_page;

      if ( empty($result->model_list) ) {
        $itemsArr['items'] = '';
      } else {
        $result = (array)$items->result->model_list;

        // 有多条商品信息
        if ( empty($result['items']->category_name) ) {
          foreach ($result['items'] as $key => $items) {
            $itemsArr['items'][$key] = $this->juItemsToArr((array)$items);
          }
        } else {
          $itemsArr['items'][0] = $this->juItemsToArr((array)$result['items']);
        }
      }

      return $itemsArr;
    }

    // 把聚划算的二级以下的结果变成数组
    public function juItemsToArr ($result) {
      $itemsArr = $result;
      $item_usp_list = (array)$itemsArr['item_usp_list'];
      $itemsArr['item_usp_list'] = $item_usp_list['string'];
      $item_usp_list = $itemsArr['price_usp_list'];
      $itemsArr['price_usp_list'] = (string)$item_usp_list->string;
      $usp_desc_list = (array)$itemsArr['usp_desc_list'];
      $itemsArr['usp_desc_list'] = $usp_desc_list['string'];

      return $itemsArr;
    }

    // 给数组的每个商品信息加入淘口令
    public function addTaoKouLing($itemCoupons)
    {
      if ($itemCoupons == []) {
        return [];
      }

      foreach ($itemCoupons as $key => $value) {
        $tpwdInfo = $this->createTpwdParaFromApi($value);
        $itemCoupons[$key]['tkl'] = (string)$this->taobao->tbkTpwdCreate($tpwdInfo)->data->model;
      }

      return $itemCoupons;
    }

    // 给数组的每个商品信息加入加密的图片地址
    public function addImageEncrypt($itemCoupons)
    {
      if ($itemCoupons == []) {
        return [];
      }

      foreach ($itemCoupons as $key => $value) {
        $imageEncryptPath = $this->encryptImage($value['pict_url']);
        $itemCoupons[$key]['image_encrypt'] = $imageEncryptPath;
      }

      return $itemCoupons;
    }

    // 将淘宝客商品查询的商品信息转换成数组
    public function makeItemsXmlToArray ($items, $param)
    {
      $itemsArr = [];

      if ($items->total_results > 1 && !empty($param['page_size']) && $param['page_size'] > 1) {
        $itemsInfo = (array)$items->results;
        foreach ($itemsInfo['n_tbk_item'] as $key => $info) {
          $itemsArr[$key] = (array)$info;
        }
      } elseif ($items->total_results == 1 || (!empty($param['page_size']) && $param['page_size'] = 1)) {
        $itemsArr[0] = (array)$items->results->n_tbk_item;
      }

      return $itemsArr;
    }
}
