@extends('admin.layouts.form.index')

@section('title', $title)
@section('headcss')

@stop
@section('content')
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
    @include('admin.layouts.form._errors')
    @include('admin.layouts.form._tips')
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>编辑优惠券分类</h5>
            <div class="ibox-tools">
                <a class="dropdown-toggle" href="{{ route('couponCategorys.index') }}">
                    <i class="fa fa-list"></i> 优惠券分类列表
                </a>
            </div>
        </div>
        <div class="ibox-content">
          <div class="row form-body form-horizontal m-t">
            <div class="col-md-12 droppable sortable ui-droppable ui-sortable">
              <form action="{{ route('couponCategorys.update', $couponCategory->id) }}" method="POST" enctype="multipart/form-data">
              		{{ csrf_field() }}
                  {{ method_field('PATCH')}}
                  <div class="col-md-12">
                      <div class="form-group">
                          <label class="col-sm-3 control-label">分类名称：</label>
                          <div class="col-sm-9">
                              <input type="text" name="category_name" value="{{ $couponCategory->category_name }}" class="form-control" required placeholder="请输入优惠券的分类名称">
                              <span class="help-block m-b-none">分类名称最多由10个汉字组成</span>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-3 control-label">排序：</label>
                          <div class="col-sm-9">
                              <input type="number" required name="order" class="form-control" value="{{ $couponCategory->order }}" placeholder="请输入排序数值">
                              <span class="help-block m-b-none">值越小，排名越靠前！</span>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-3 control-label">上传图片：</label>
                          <div class="col-sm-9"><img src="{{ $couponCategory->image_small }}" style="max-width:41px;" />
                              <input type="file" name="image_small" class="form-control">
                              <span class="help-block m-b-none text-warning"><strong>提示：</strong>上传图片的大小为：41px*41px</span>
                          </div>
                      </div>
											<div class="form-group">
													<label class="col-sm-3 control-label">字体图标：</label>
													<div class="col-sm-9">
															<input type="text" name="font_icon" value="{{ $couponCategory->font_icon }}" class="form-control"  placeholder="请输入字体图标的标签">
															<span class="help-block m-b-none">字体图标由完成的i标签组成</span>
													</div>
											</div>
                      <div class="form-group">
                          <label class="col-sm-3 control-label">是否显示：</label>
                          <div class="col-sm-9">
                              <label class="radio-inline">
                                  <input type="radio" @if($couponCategory->is_show == '1') checked @endif value="1" id="optionsRadios1" name="is_show">显示</label>
                              <label class="radio-inline">
                                  <input type="radio" @if($couponCategory->is_show == '0') checked @endif value="0" id="optionsRadios2" name="is_show">不显示</label>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-3 control-label">商品组合条件：</label>
                          <div class="col-sm-9">
                            {!! $selfWhereView !!}
                          </div>
                      </div>
                  </div>

                  <div style="clear:both;"></div>
                  <div class="hr-line-dashed"></div>

                  <h3 class="text-center"><strong>第一组商品组合条件</strong></h3>
                  <div class="col-md-5" id="groupselect1">
                      <div class="form-group" id="select1">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group1[0][cate]">
                                <option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group1[1][cate]">
																<option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group" id="select1">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group1[2][cate]">
																<option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group1[3][cate]">
																<option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-7" id="groupwords1">
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group1[0][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group1[1][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group1[2][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group1[3][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                  </div>

                  <div style="clear:both;"></div>
                  <div class="hr-line-dashed"></div>

                  <h3 class="text-center"><strong>第二组商品组合条件</strong></h3>
                  <div class="col-md-5" id="groupselect1">
                      <div class="form-group" id="select1">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group2[0][cate]">
																<option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group2[1][cate]">
																<option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group" id="select1">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group2[2][cate]">
																<option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-sm-5 control-label">选择字段：</label>
                          <div class="col-sm-7">
                              <select class="form-control" name="group2[3][cate]">
																<option value="goods_name" selected>商品名称</option>
                                <option value="category" >商品一级类目</option>
                              </select>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-7" id="groupwords1">
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group2[0][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group2[1][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group2[2][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                      <div class="form-group copy">
                          <label class="col-sm-4 control-label">填写关键词：</label>
                          <div class="col-sm-8">
                              <input type="text" name="group2[3][word]" class="form-control" placeholder="请输入关键词">
                          </div>
                      </div>
                  </div>

                  <div style="clear:both;"></div>
                  <div class="hr-line-dashed"></div>

                  <div class="col-md-12">
                      <div class="form-group">
                          <div class="col-sm-12 col-sm-offset-5">
                              <button class="btn btn-primary" type="submit">保存内容</button>
                          </div>
                      </div>
                  </div>
              </form>

              <div class="form-group">
                  <label class="col-sm-3 control-label">关键词填写说明：</label>
                  <div class="col-sm-9">
                      <p class="form-control-static">
                        “%关键词%”表示关键词可以出现在任何位置；<br>
                        “关键词%”表示以”关键词“开头的句子；<br />
                        “%关键词”表示以”关键词“结尾的句子。<br />
                      </p>
                  </div>
              </div>

            </div>
          </div>
        </div><!-- ibox-content -->
    </div>
  </div>
</div>
@stop
@section('footJs')

@stop
