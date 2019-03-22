<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "colin_menu".
 *
 * @property string $id 菜单表
 * @property string $name 菜单名
 * @property string $icon 图标
 * @property int $is_show 是否显示 1:显示 2:隐藏
 * @property int $sort 排序
 * @property int $create_time 创建时间
 */
class Menu extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'colin_menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_show', 'sort', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '菜单名',
            'icon' => '图标',
            'is_show' => '是否显示',
            'sort' => '排序',
            'create_time' => '创建时间',
        ];
    }

    public function create($data)
    {
        $this->name = $data['name'];
        $this->icon = $data['icon'];
        if (!$data['sort']) {
            $data['sort'] = 10;
        }
        $this->sort = $data['sort'];
        $this->is_show = $data['is_show'];
        $this->create_time = time();
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    public function edit($data)
    {
        $model = self::findOne($data['id']);
        $model->name = $data['name'];
        $model->icon = $data['icon'];
        $model->is_show = $data['is_show'];
        if (!$data['sort']) {
            $data['sort'] = 10;
        }
        $model->sort = $data['sort'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

}