<?php
/**
 * This is the template for generating a controller class within a module.
 */
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

echo "<?php\n";

$restModelClass = $generator->restNs . '\\' . $generator->generateRestClassName($generator->modelClass);
?>

namespace <?= $generator->getControllerNamespace() ?>;

use common\rest\controllers\ActiveController as BaseModel;
use <?= $restModelClass ?>;

/**
* Default controller for the `<?= $generator->restModuleID ?>` module
*/
class DefaultController extends BaseModel
{
    public $modelClass = <?= $generator->generateRestClassName($generator->modelClass) ?>::class;
}
