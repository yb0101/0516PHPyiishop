<?php
namespace frontend\controllers;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use frontend\models\SmsDemo;
use yii\data\Pagination;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\Cookie;

class MemberController extends Controller
{
    public $enableCsrfValidation = false;//关闭跨站攻击请求

    public function actionLogin()
    {
        //显示登陆页面
        $model = new LoginForm();

        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
            //var_dump($model);exit;
            if ($model->validate()) {
                //认证
                if ($model->login()) {
                    \Yii::$app->session->setFlash('success', '登录成功');
                    return $this->redirect(['show']);
                }
            }
        }
        return $this->renderPartial('login');
    }

//添加地址页面功能
    public function actionAddress()
    {
        $model = new Address();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
            //var_dump($model);exit;
            if ($model->validate()) {
                //var_dump($model);exit;
                $id = \Yii::$app->user->getId();
                //var_dump($id);exit();
                $model->user_id = $id;
                $model->address = $model->province . $model->city . $model->area . $model->address;
                $model->save(false);
                \Yii::$app->session->setFlash('seccuss', '添加地址成功');
                return $this->redirect(['address']);
            } else {
                \Yii::$app->getErrorHandler();
            }
        }
        $address=Address::find()->where(['user_id'=>\Yii::$app->user->id])->all();
        return $this->renderPartial('address', ['model' => $model,'address'=>$address]);
    }

//编辑地址页面
    public function actionEdit($id)
    {
        $model = Address::findOne(['id' => $id]);
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
            if ($model->validate()) {
                $model->save();
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['address']);
            } else {
                var_dump($model->getErrors());
            }
        }
        //var_dump($model);exit;
        $address=Address::find()->where(['user_id'=>\Yii::$app->user->id])->all();
        return $this->renderPartial('edit', ['model' => $model,'address'=>$address]);
    }
    //地址列表功能
    public function actionLiist(){
        //获取数据
        $address=Address::find()->where(['user_id'=>\Yii::$app->user->id])->all();
        //分配数据到视图
        //var_dump($address);exit;
        return $this->render('address',['address'=>$address]);
    }
    //AJAX删除数据
    public function actionDelete(){
        //获取ID
        $id=\Yii::$app->request->post('id');
        //var_dump($id);exit;
        $model =Address::findOne(['id'=>$id]);
        if($model){
            $model->delete();
            return "success";
        }
        return "faile";
    }

//展示注册页面
    public function actionRegist()
    {
        $model = new Member();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            //  var_dump($request->post());die;
            $model->load($request->post(), '');
            //var_dump($model);exit;
            if ($model->validate()) {
                //var_dump($model);exit;
                $model->created_at = time();
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->repassword);
                $model->save(false);
                \Yii::$app->session->setFlash('success', '注册成功');
                return $this->redirect(['index']);
            } else {
                \Yii::$app->getErrorHandler();
            }
        }
        //$this->layout=false;//不加载布局页面(方法一)
        return $this->renderPartial('regist', ['model' => $model]);//方法二
        //return $this->render('regist');
    }

//ajax验证用户唯一性
    public function actionValidateUser($username)
    {
        $a = Member::findOne(['username' => $username]);
        if ($a) {
            //用户名已经存在
            return 'false';
        } else {
            //用户名可以注册
            return 'true';
        }
    }

    public function actionIndex()
    {
        //var_dump(\Yii::$app->user->identity);
        return $this->renderPartial('index');
    }

    //注销登陆
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success', '注销成功');
        return $this->redirect(['show']);
    }

    //测试REDIS
    public function actionRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis->set('age', '29');
        \Yii::$app->session->setFlash('success', '设置成功');
    }

    public function actionPhpinfo()
    {
        phpinfo();
    }

    //测试短信革命呢
    public function actionSms()
    {
        $demo = new SmsDemo(
            "LTAIaGKxVSdRcAH6",//ak
            "vOcZwQ76qPPLaXHvo0zD9RdHImpu6z"//sk
        );
        $phnoe = \Yii::$app->request->post('phone');
        $code = mt_rand(100000, 999999);
        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "波胖商城欢迎你", // 短信签名
            "SMS_97960025", // 短信模板编号
            $phnoe, // 短信接收者
            Array(  // 短信模板中字段的值
                "code" => $code,
            )

        );
        $phone = \Yii::$app->request->post('phone');
        \Yii::$app->session->set('_code' . $phone, $code);//把短信保存在session中
        print_r($response);

    }

    public function actionValidateSms($sms, $phone)
    {
        $code = \Yii::$app->session->get('_code' . $phone);
        if ($code == null || $code != $sms) {
            return false;
        } else {
            return true;
        }

    }

    //首页展示功能
    public function actionShow()
    {
//获取顶级分类数据
        $model = GoodsCategory::find()->where(['parent_id' => 0])->all();
        //var_dump($model);exit;
        //分配数据到视图
        return $this->renderPartial('index', ['model' => $model]);
    }

    //商品列表功能
    public function actionList($category_id)
    {
        $category = GoodsCategory::findOne(['id' => $category_id]);
//        var_dump($category);exit;//根据传过来category_id找到这个类
        $query = Goods::find();//查询商品
        if ($category->depth == 2) {//那么就是三级分类
            $query->andWhere(['goods_category_id' => $category_id]);
        } else {//非三级的
            $ids = [];
//            foreach ($category->children()->andWhere(['depth' => 2])->all() as $category3) {
//                $ids[] = $category3->id;
////                var_dump($ids);exit;
//            }
            $ids = $category->children()->select('id')->andWhere(['depth' => 2])->column();
//            var_dump($ids);exit;
            $query->andWhere(['in', 'goods_category_id', $ids]);
        }

        $pager = new Pagination();
        $pager->totalCount = $query->count();
        $pager->defaultPageSize = 20;

        $goods = $query->limit($pager->limit)->offset($pager->offset)->all();
//        var_dump($goods);exit;
        //分配数据到视图
        return $this->renderPartial('list', ['goods' => $goods, 'pager' => $pager]);
    }

//商品详情页功能
    public function actionAlist($id)
    {
        $good = Goods::findOne(['id' => $id]);
        //var_dump($good);exit;
        //取出小图图片
        $pictures = GoodsGallery::find()->where(['goods_id' => $id])->all();
        //分配数据到视图
        return $this->renderPartial('goods', ['good' => $good, 'pictures' => $pictures]);

    }

////添加到购物车
//    public function actionAddtocart($good_id, $amount)
//    {
//        //判断是不是登陆，
//        if (\Yii::$app->user->isGuest) {//未登录购物车数据保存在COOKIE中
//            //先看看以前购物车有没有数据
//            $cookies = \Yii::$app->request->cookies;
//            $value = $cookies->getValue('cart');
//            if ($value) {
//                $carts = unserialize($value);
//            } else {
//                $carts = [];
//            }
//            //var_dump($carts);exit;
//
//        //检查购物车中是否存在当前需要添加的商品
//        if (array_key_exists($good_id, $carts)) {
//            $carts[$good_id] += $amount;
//        } else {
//            $carts[$good_id] = intval($amount);
//        }
//        $cookies = \Yii::$app->response->cookies;
//        $cookie = new Cookie();
//        $cookie->name = 'carts';
//        $cookie->value = serialize($carts);
//        $cookie->expire = time() + 7 * 24 * 3600;//过期时间戳
//        $cookies->add($cookie);
//    }else{
//    //已经登陆，购物车数据存在数据库中
//}
////直接跳转到购物车
//return $this->redirect(['cart']);
//}
////购物车功能
//public function actionCart(){
////获取购物车数据
//    if(\Yii::$app->user->isGuest){//从COOKIE中获取数据
//        $cookies=\Yii::$app->request->cookies;
//        $value=$cookies->getValue('carts');
//        $carts = unserialize($value);
//        var_dump($carts);exit;
//        $models = Goods::find()->where(['in','id',array_keys($carts)])->all();
//    }
//  return $this->render('cart',['model'=>$models,'carts'=>$carts]);
//}
//添加到购物车页面  完成添加到购物车的操作
    public function actionAddtocart($goods_id, $amount)
    {

        if (\Yii::$app->user->isGuest) {
            //未登录 购物车数据存cookie

            //写入cookie  $goods_id = 1  ,$amount = 2
            /*$carts = [
                ['goods_id'=>1,'amount'=>2],
                ['goods_id'=>2,'amount'=>10],
            ];*/
            /*$carts = [
                1=>2,2=>10
            ];*/
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if ($value) {
                $carts = unserialize($value);
            } else {
                $carts = [];
            }
            //var_dump($carts);exit;
            //检查购物车中是否存在当前需要添加的商品
            if (array_key_exists($goods_id, $carts)) {
                $carts[$goods_id] += $amount;
            } else {
                $carts[$goods_id] = intval($amount);//强制转化成数值类型
            }

            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time() + 7 * 24 * 3600;//过期时间戳
            $cookies->add($cookie);

            //var_dump($cookies);exit;
        } else {
            //先同步数据库，把Cookie中的数据保存到数据库中
            //第一步先取出cookie中的购物车数据
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if ($value) {
                $carts = unserialize($value);//$carts = [1=>2,2=>10]
            }
            //遍历取出的数据,如果有就合并数据。没有就新添加一条数据
            foreach ($carts as $goods_id => $amount) {
                $member_id = \Yii::$app->user->getId();
                $result = Cart::findOne(['goods_id' => $goods_id, 'member_id' => $member_id]);//必须加上用户ID判断
                if ($result) {
                    //存在就累加
                    $result->amount += $amount;
                    $result->save();
                } else {
                    //不存在就新添一条
                    $model = new Cart();
                    $model->member_id = $member_id;
                    $model->goods_id = $goods_id;
                    $model->amount = $amount;
                    $model->save();
                }
                return $this->redirect(['cart']);
            }

            //摧毁Cookie
            $cookies = \Yii::$app->response->cookies;
            $cookies->remove($carts);
        }
    }

//            //已登录 购物车数据存数据库
//            $model = new Cart();
//            $member_id = \Yii::$app->user->getId();
//            $cart = Cart::findOne(['goods_id' => $goods_id, 'member_id' => $member_id]);
//            if ($cart) {
//                $cart->amount += $amount;
//                $cart->save();
//            } else {
//                $model->member_id = $member_id;
//                $model->goods_id = $goods_id;
//                $model->amount = $amount;
//                if ($model->validate()) {
//                    $model->save();
//                }
//
//            }
//
//        }
    //直接跳转到购物车
    //var_dump($cookies);exit;


    //购物车页面
    public function actionCart()
    {
        //获取购物车数据
        if (\Yii::$app->user->isGuest) {
            //从cookie
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if ($value) {
                $carts = unserialize($value);//$carts = [1=>2,2=>10]
            } else {
                $carts = [];
            }
            $models = Goods::find()->where(['in', 'id', array_keys($carts)])->all();

        } else {
            //从数据库中获取数据
            $member_id = \Yii::$app->user->getId();
            $amount = \Yii::$app->request->post('amount');
            $goods = Cart::find()->select(['goods_id', 'amount'])->where(['member_id' => $member_id])->asArray()->all();
            //var_dump($goods);exit;
            $carts = [];
            foreach ($goods as $good) {
                $carts[$good['goods_id']] = $good['amount'];
            }
            $models = Goods::find()->where(['in', 'id', array_keys($carts)])->all();
        }
        //var_dump($models);exit;
        return $this->renderPartial('cart', ['models' => $models, 'carts' => $carts]);
    }

    //AJAX修改购物车商品数量
    public function actionAjax()
    {
        // goods_id  amount  2=>1
        $goods_id = \Yii::$app->request->post('goods_id');
//        var_dump($goods_id);exit;
        $amount = \Yii::$app->request->post('amount');
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if ($value) {
                $carts = unserialize($value);
            } else {
                $carts = [];
            }

            //检查购物车中是否存在当前需要添加的商品
            if (array_key_exists($goods_id, $carts)) {
                $carts[$goods_id] = $amount;
            }

            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time() + 7 * 24 * 3600;//过期时间戳
            $cookies->add($cookie);
            //echo $amount;
        } else {
            //既然是在登陆后的购物车中 那肯定是存在这样的商品的，既然存在这样的商品，那么直接修改数量就行啦
            $member_id = \Yii::$app->user->getId();
            $carts = Cart::findOne(['member_id' => $member_id, 'goods_id' => $goods_id]);
            $carts->amount = $amount;//不用判断加减，因为是直接赋值过去的，重新更新
            $carts->save();
        }
    }

    //静态删除
    public function actionDel()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        return Cart::delCart(intval($goods_id));
    }

    //订单处理
    public function actionOrder()
    {
        $model = new Order();
        if (\Yii::$app->user->isGuest) {
            return $this->render('login');
        }
        //获取用户收货地址
        $address = Address::find()->where(['user_id' => \Yii::$app->user->getId()])->all();
        //获取购物车信息
        $carts = Cart::find()->where(['member_id' => \Yii::$app->user->getId()])->all();
        //var_dump($carts);exit;
        $cars = [];
        foreach ($carts as $cart) {
            $cars[] = $cart;
            //随带处理carts

        }
        //var_dump($cars);exit;
        // $goods=Goods::find()->where(['in','id',$a])->all();
        //var_dump($goods);exit;
        //var_dump($carts);exit;
        //var_dump(\Yii::$app->user->getId());EXIT;
        $deliveries = Order::$deliveries;
        $plays = Order::$play;
        return $this->renderPartial('order', ['address' => $address, 'cars' => $cars, 'deliveries' => $deliveries, 'plays' => $plays]);
    }

    //提交订单
    public function actionAddOrder($address_id, $delivery_id, $pay_id)
    {
        //实例化模型
        $model = new Order();
        //var_dump($model);exit;
        //开启事物
        $transaction = \Yii::$app->db->beginTransaction();
        $member_id = \Yii::$app->user->getId();
        $carts = Cart::find()->where(['member_id' => $member_id])->all();
        try{
        //获取地址信息
        $address = Address::findOne(['user_id' => $member_id, 'id' => $address_id]);
        $model->member_id = $member_id;
        $model->name = $address->username;
        $model->province = $address->province;
        $model->city = $address->city;
        $model->area = $address->area;
        $model->address = $address->address;
        $model->tel = $address->tel;
        //获取配送信息
        $model->delivery_id = $delivery_id;
        $model->delivery_name = Order::$deliveries[$delivery_id][0];
        $model->delivery_price = Order::$deliveries[$delivery_id][1];
        //获取付款信息
        $model->payment_id = $pay_id;
        $model->payment_name = Order::$play[$pay_id][0];
        $model->total = 0;
        $model->status = 1;
        $model->create_time = time();
        $model->save(false);
        //处理订单商品表数据
        //获取购物车数据
        $total = 0;
        foreach ($carts as $cart) {
            $goods = Goods::findOne(['id' => $cart->goods_id]);
            $order_goods = new OrderGoods();
            if ($cart->amount <= $goods->stock) {
                $order_goods->order_id = $model->id;//订单号
                $order_goods->goods_id = $goods->id;
                $order_goods->goods_name = $goods->name;
                $order_goods->logo = $goods->logo;
                $order_goods->price = $goods->shop_price;
                $order_goods->amount = $cart->amount;
                $order_goods->total = $cart->amount * $goods->shop_price;
                $order_goods->save();
                //改变订单表的统计金额,将购买的每个商品的价钱相加
                $total += $order_goods->total;
                //下单成功后改变商品库存
                $goods->stock -= $cart->amount;
                $goods->save();
                //下单成功后清除购物车
                $cart->delete();
            } else {
                //（检查库存，如果库存不够抛出异常）
                throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
            }
        }
        //订单生成成功后，计算订单表总金额
        $model->total = $total;
        $model->update(false);
        //提交事务
        $transaction->commit();
    }catch (Exception $e)
{
    //不能下单,需要回滚
$transaction->rollBack();
}


var_dump($model->getErrors());exit;



}
//查看订单状态
public function actionSorder(){
       $member_id=\Yii::$app->user->id;
       $model=new Order();
       $orders=Order::find()->where(['member_id'=>$member_id])->all();
        return $this->renderPartial('ordercontent',['orders'=>$orders,'model'=>$model]);
}
//查看订单详情
public function actionOd($id){
    $member_id=\Yii::$app->user->getId();
    $contents=OrderGoods::find()->where(['order_id'=>$id])->all();
    return $this->renderPartial('od',['contents'=>$contents]);
}

}
