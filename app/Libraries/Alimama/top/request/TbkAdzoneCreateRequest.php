<?php

namespace App\Libraries\Alimama\top\request;

use App\Libraries\Alimama\top\RequestCheckUtil;

/**
 * TOP API: taobao.tbk.adzone.create request
 *
 * @author auto create
 * @since 1.0, 2017.09.07
 */
class TbkAdzoneCreateRequest
{
	/**
	 * 广告位名称，最大长度64字符
	 **/
	private $adzoneName;

	/**
	 * 网站ID
	 **/
	private $siteId;

	private $apiParas = array();

	public function setAdzoneName($adzoneName)
	{
		$this->adzoneName = $adzoneName;
		$this->apiParas["adzone_name"] = $adzoneName;
	}

	public function getAdzoneName()
	{
		return $this->adzoneName;
	}

	public function setSiteId($siteId)
	{
		$this->siteId = $siteId;
		$this->apiParas["site_id"] = $siteId;
	}

	public function getSiteId()
	{
		return $this->siteId;
	}

	public function getApiMethodName()
	{
		return "taobao.tbk.adzone.create";
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function check()
	{

		RequestCheckUtil::checkNotNull($this->adzoneName,"adzoneName");
		RequestCheckUtil::checkMaxLength($this->adzoneName,64,"adzoneName");
		RequestCheckUtil::checkNotNull($this->siteId,"siteId");
	}

	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
