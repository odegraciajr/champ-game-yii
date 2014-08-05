<?php
/* @var $this UserController */
/* @var $model SignupForm */
/* @var $form CActiveForm */
?>
<div id="signup">
	<div class="sign-login-header">
		<h3 class="title">Register</h3>
	</div>
	<div class="form sign-login">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'user-signup-form',
		'enableAjaxValidation'=>false,
		'enableClientValidation'=>false,
		'focus'=>array($model,'email'),
	)); ?>
		<div class="row">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name'); ?>
			<?php echo $form->error($model,'name'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email'); ?>
			<?php echo $form->error($model,'email'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password'); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>
		
		<div class="row">
			<?php echo $form->labelEx($model,'password2'); ?>
			<?php echo $form->passwordField($model,'password2'); ?>
			<?php echo $form->error($model,'password2'); ?>
		</div>
		<input type="hidden" name="ajax" value="user-signup-form"/>
	<?php $this->endWidget(); ?>
	</div><!-- form -->
	<div class="form-submit btn-wrap-default">
		
		<button class="champ-btn-default" id="signup-btn">Sign Up</button>
	</div>
</div>
<?php
Yii::app()->getClientScript()->registerScript('#user-signup-form', "
	$('#signup-btn').click(function(e){
		var req = $.post( '$ajaxUrl', $( '#user-signup-form' ).serialize() );
		
		req.done(function( data ) {
			if( data.success ){
				window.location.replace(data.redirect);
			}else{
				alert( data );
			}
		});
		
		e.preventDefault();
		return false;
	});
", CClientScript::POS_END);
?>