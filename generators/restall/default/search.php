<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
//$searchModelClass = StringHelper::basename($generator->searchModelClass);
$searchModelClass = StringHelper::basename($generator->generateSearchClassName($generator->modelClass));
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}

$rules = $generator->generateSearchRules($tableSchema);
$labels = $generator->generateSearchLabels($tableSchema);
$searchAttributes = $generator->getSearchAttributes($tableSchema);
$searchConditions = $generator->generateSearchConditions($tableSchema);

echo "<?php\n";

if ($generator->ns !== $generator->searchNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $searchModelClass;
}
?>

namespace <?= $generator->searchNs ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= '\\' . ltrim($generator->generateSearchBaseClassName($modelClass), '\\') ?> as BaseModel;

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'enableMultiSort' => true,
                'sortParam' => $this->getSortParamName(),
            ],
            'pagination' => [
                'pageParam' => $this->getPageParamName(),
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }
    
    public function getPageParamName($splitor = '-'){
        $name = "<?= isset($modelAlias) ? $modelAlias : $modelClass ?>Page";
        return \Yii::$app->czaHelper->naming->toSplit($name);
    }
    
    public function getSortParamName($splitor = '-'){
        $name = "<?= isset($modelAlias) ? $modelAlias : $modelClass ?>Sort";
        return \Yii::$app->czaHelper->naming->toSplit($name);
    }
}
