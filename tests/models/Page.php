<?php

namespace tests\models;

use DevGroup\Entity\traits\BaseActionsInfoTrait;
use DevGroup\Entity\traits\EntityTrait;
use DevGroup\Entity\traits\SeoTrait;
use DevGroup\Entity\traits\SoftDeleteTrait;
use yii\db\ActiveRecord;

class Page extends ActiveRecord
{
    use EntityTrait;
    use SeoTrait;
    use BaseActionsInfoTrait;
    use SoftDeleteTrait;

    protected $rules = [
        ['url', 'string', 'max' => 255],
        ['url', 'required'],
    ];

    protected $attributeLabels = [
        'url' => 'Page url',
    ];

    public $isDeletedAttribute = 'deleted';

    public static function tableName()
    {
        return 'page';
    }

    public function beforeValidate()
    {
        $this->url = empty($this->slug) === false ? '/' . $this->slug : '';
        return parent::beforeValidate();
    }
}
