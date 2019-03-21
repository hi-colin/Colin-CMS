<?php
/**
 * User: Colin
 * Time: 2019/3/16 16:04
 */

namespace backend\controllers;

use backend\models\Role;
use Yii;
use backend\models\User;
use yii\data\Pagination;

class UserController extends BaseController
{
    public function actionIndex()
    {
        $query = User::find()->joinWith('role');
        $search = Yii::$app->request->get('search');
        $query = $this->condition($query, $search);
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => 10,
        ]);
        $models = $query
            ->orderBy('id desc')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        return $this->render('index', compact('models', 'pagination', 'search'));
    }

    public function condition($query, $search)
    {
        if (isset($search['name']) && $search['name']) {
            $query = $query->andWhere(['like', 'colin_user.name', $search['name']]);
        }
        if (isset($search['b_time']) && $search['b_time']) {
            $bTime = strtotime($search['b_time'] . ' 00:00:00');
            $query = $query->andWhere(['>=', 'create_time', $bTime]);
        }
        if (isset($search['e_time']) && $search['e_time']) {
            $eTime = strtotime($search['e_time'] . ' 23:59:59');
            $query = $query->andWhere(['<=', 'create_time', $eTime]);
        }
        return $query;
    }

    public function actionCreate()
    {
        $roles = Role::find()->orderBy('id asc')->all();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $member = new User();
            $res = $member->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('create', compact('roles'));
    }

        public function actionChangeStatus()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($post['id'] == 1) {
                return $this->json(100, 'admin禁止禁用');
            }
            $user = User::findOne($post['id']);
            $status = $post['status'] == 1 ? 2 : 1;
            $user->status = $status;
            if (!$user->save(false)){
                return $this->json(100, '操作失败');
            }
            return $this->json(200, '操作成功');
        }
    }

    public function actionDel()
    {
        $id = (int)Yii::$app->request->get('id');
        if ($id == 1) {
            return $this->json(100, 'admin禁止删除');
        }
        $model = User::findOne($id);
        $res = $model->delete();
        if (!$res) {
            return $this->json(100, '删除失败');
        }
        return $this->json(200, '删除成功');
    }

    public function actionBatchDel()
    {
        $idArr = Yii::$app->request->get('idArr');
        if (in_array(1, $idArr)) {
            return $this->json(100, 'admin禁止删除');
        }
        $res = User::deleteAll(['in', 'id', $idArr]);
        if (!$res) {
            return $this->json(100, '批量删除失败');
        }
        return $this->json(200, '批量删除成功');
    }



}