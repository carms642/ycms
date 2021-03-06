<?php
/**
 * 文章
 */
class ArticleController extends AdminBaseController
{
	public function init()
	{
		parent::init();
		$this->menu=array(
			array('label'=>'管理', 'icon'=>'align-justify', 'url'=>array('/admin/article/index')),
			array('label'=>'创建', 'icon'=>'plus', 'url'=>array('/admin/article/create')),
		);
	}

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
	public function actionCreate()
	{
		$model=new ArticleForm;
		if(isset($_POST['ArticleForm']))
		{
			$model->attributes=$_POST['ArticleForm'];
			$model->image_file=CUploadedFile::getInstance($model, 'image_file');
			if($model->save()){
				Yii::app()->user->setFlash('success', "创建成功！");
				$this->redirect(array('index'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	* Updates a particular model.
	* If update is successful, the browser will be redirected to the 'view' page.
	* @param integer $id the ID of the model to be updated
	*/
	public function actionUpdate($id)
	{
		$article=$this->loadModel($id);
		$model=new ArticleForm;
		$model->attributes = $article->attributes;
		$model->attributes = $article->Profile->attributes;
		$model->article = $article;
		if(isset($_POST['ArticleForm']))
		{	
			$model->attributes=$_POST['ArticleForm'];
			$model->image_file=CUploadedFile::getInstance($model, 'image_file');
			if($model->save()) {
				Yii::app()->user->setFlash('success', "修改成功！");
				$this->refresh();
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
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
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	* Lists all models.
	*/
	public function actionIndex()
	{
		$model=new Article('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Article']))
			$model->attributes=$_GET['Article'];

		$this->render('index',array(
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
		$model=Article::model()->with('Profile')->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='article-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}