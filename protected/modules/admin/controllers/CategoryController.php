<?php

class CategoryController extends AdminBaseController
{
	public $menu=array(
		array('label'=>'管理', 'icon'=>'align-justify', 'url'=>array('index')),
		array('label'=>'创建', 'icon'=>'plus', 'url'=>array('create')),
	);

	/**
	* Displays a particular model.
	* @param integer $id the ID of the model to be displayed
	*/
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	* Creates a new model.
	* If creation is successful, the browser will be redirected to the 'view' page.
	*/
	public function actionCreate() {
    	$model = new Category;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];
            $parent_node = isset($_POST['Category']['id']) ? $_POST['Category']['id'] : 0;
            if ($parent_node != 0) {
                $node = Category::model()->findByPk($parent_node);
                $model->appendTo($node);
            }
            if ($model->saveNode()){
            	Yii::app()->user->setFlash('success', "创建成功！");
                $this->redirect(array('index'));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

	/**
	* Updates a particular model.
	* If update is successful, the browser will be redirected to the 'view' page.
	* @param integer $id the ID of the model to be updated
	*/
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];
            $parent_node = $_POST['Category']['id'];
            if ($parent_node != 0) {
                $node = Category::model()->findByPk($parent_node);
                $parent=$model->parent()->find();
                if ($node->id !== $model->id && $node->id !== $parent->id) {
                    $model->moveAsLast($node);

                    if ($model->saveNode())
                    	Yii::app()->user->setFlash('success', "保存成功！");
                        $this->redirect(array('index'));
                }else{
                	if ($model->saveNode())
                		Yii::app()->user->setFlash('success', "保存成功！");
                        $this->redirect(array('index'));
                }
            }else {
                if(!$model->isRoot()){
                $model->moveAsRoot();
                }
                if ($model->saveNode())
                	Yii::app()->user->setFlash('success', "保存成功！");
                    $this->redirect(array('index'));
            }
        }

		$this->render('update',array(
			'model'=>$model,
			));
	}

	public function actionMove($id,$updown)
	{
		$model=$this->loadModel($id);

		if($updown=="down") {
			$sibling=$model->next()->find();
			if (isset($sibling)) {
				if($model->moveAfter($sibling))
					Yii::app()->user->setFlash('success', "移动成功！");
				$this->redirect(array('index'));
			}
			$this->redirect(array('index'));
		}
		if($updown=="up"){
			$sibling=$model->prev()->find();
			if (isset($sibling)) {
				if($model->moveBefore($sibling))
					Yii::app()->user->setFlash('success', "移动成功！");
				$this->redirect(array('index'));
			}
			$this->redirect(array('index'));
		}
	}

	/**
	* Deletes a particular model.
	* If deletion is successful, the browser will be redirected to the 'admin' page.
	* @param integer $id the ID of the model to be deleted
	*/
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->deleteNode();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				Yii::app()->user->setFlash('success', "删除成功！");
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	* Lists all models.
	*/
	public function actionIndex()
	{
		$data=Category::tree();
		$this->render('index',array(
			'data'=>$data,
		));
	}

	/**
	* Manages all models.
	*/
	public function actionAdmin()
	{
		$model=new Category('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Category']))
			$model->attributes=$_GET['Category'];
		
		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	* Returns the data model based on the primary key given in the GET variable.
	* If the data model is not found, an HTTP exception will be raised.
	* @param integer the ID of the model to be loaded
	*/
	public function loadModel($id)
	{
		$model=Category::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	* Performs the AJAX validation.
	* @param CModel the model to be validated
	*/
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='category-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
