<?php
//会员站内信模块
/**
 * @author   poctsy  <poctsy@foxmail.com>
 * @copyright Copyright (c) 2013 poctsy
 * @link      http://www.fircms.com
 * @version   MessageController.php  23:56 2013年10月02日
 */
class MessageController extends FAdminController
{

    public $layout='application.modules.admin.views.layouts.column2';

    public function filters() {
        return array(
            array('auth.filters.AuthFilter'),
        );
    }



    /**
     * Manages all models.
     */
    public function actionIndex()
    {
        $model=new Message('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Message']))
            $model->attributes=$_GET['Message'];

        $this->render('index/index',array(
            'model'=>$model,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model=new Message('adminSearch');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Message']))
            $model->attributes=$_GET['Message'];

        $this->render('admin/admin',array(
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
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if(!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
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

        if(isset($_POST['Message']))
        {
            $model->attributes=$_POST['Message'];
            if($model->save())
                $this->redirect(array('admin'));
        }

        $this->render('admin/update',array(
            'model'=>$model,
        ));
    }
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Message the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=Message::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Message $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='message-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }







    public function actionSend()
    {
        $model=new Message;
        $model->scenario='send';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Message']))
        {
            $model->attributes=$_POST['Message'];

            if($model->save())
                $this->redirect(array('index'));
        }
        $this->render('send/send',array(
            'model'=>$model,
        ));
    }

    public function actionReply($user)
    {
        $getUser=User::model()->findbypk(Yii::app()->request->getParam('user'));
        if($getUser==NULL)
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));

        $to_user_name=$getUser->username;

        $model=new Message;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if($getUser!=NULL)$model->to_user_id==$getUser->id;
        $model->from_user_id=Yii::app()->user->id;
        if(isset($_POST['Message']))
        {
            $model->attributes=$_POST['Message'];

            if($model->save())
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
        $this->render('reply/reply',array(
            'model'=>$model,
            'to_user_name'=>$to_user_name,
        ));
    }


    /**
     * Manages all models.
     */
    public function actionView($user)
    {
        $getUser=User::model()->findbypk(Yii::app()->request->getParam('user'));
        if($getUser==NULL)
         $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));


        $to_user_id=$getUser->id;
        $user_id=Yii::app()->user->id;
        $criteria = new CDbCriteria;
        $criteria->addInCondition('from_user_id', array($user_id,$to_user_id),'and');
        $criteria->addInCondition('to_user_id', array($user_id,$to_user_id),'and');
       // $criteria->addCondition("from_user_id=$from_user_id","OR");
       // $criteria->addCondition("to_user_id=$to_user_id","OR");
        $dataProvider=new CActiveDataProvider('Message',array(
            'criteria'=>$criteria,
        ));
        $this->render('view/view',array(
            'dataProvider'=>$dataProvider,
            'to_user_id'=>$to_user_id,
        ));
    }

}
