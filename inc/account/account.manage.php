<?php
/****************************************************************************/
/*  						< Emerald Sanctum >  							*/
/*              Copyright (C) <2018> <emeraldsanctum.com>   		        */
/*					  < https://emeraldsanctum.com >					 	*/
/*																			*/
/*			Origional MangosWeb v4 (C) 2017 Mistvale.com					*/
/*			Original MangosWeb Enhanced (C) 2010-2011 KeysWow				*/
/*			Original MangosWeb (C) 2007, Sasha, Nafe, TGM, Peec				*/
/****************************************************************************/

//========================//
if(INCLUDED !== TRUE) 
{
	echo "Not Included!"; 
	exit;
}
$pathway_info[] = array('title' => $lang['account_manage'], 'link' => '');
// ==================== //

// check if the user is logged in. if not, redirect
if($Account->isLoggedIn() == FALSE)
{
    redirect('?p=account&sub=login',1);
}

// Enter the page descrition
$PAGE_DESC = $lang['account_manange_intro'];

// First we need to load the users profile
$profile = $Account->getProfile($user['id']);

// Load secret questions as $secret_1
$secret_questions = $Account->getSecretQuestions();

// ==== Functions ==== //

//	************************************************************
// Change Email, Buffer function for the SDL

function changeEmail()
{
	global $lang, $user, $Account, $RDB, $mwe_config;

	if(!$mwe_config['allow_user_email_change'])
		return TRUE;
	
	$newemail = trim($_POST['email']);
	
	// First we check if the email is valid
	if($Account->isValidEmail($newemail))
	{
		//Next we see if the email is used already
		$email = $RDB->selectCell("SELECT `email` FROM `account` WHERE `id`='".$user['id']."'");
		if($newemail != $email)
		{
			if($Account->isAvailableEmail($newemail) == FALSE)
			{
				output_message('validation','<b>'.$lang['register_email_used'].'</b><meta http-equiv=refresh content="3;url=?p=account&sub=manage">');
				return FALSE;
			}
		}
		
		// Now we set the email by using the SDL
		if($Account->setEmail($user['id'], $newemail) == TRUE)
		{
			return TRUE;
		}
		else
		{
			output_message('error','Error Setting Email! <meta http-equiv=refresh content="3;url=?p=account&sub=manage">');
		}
	}
	else
	{
		output_message('validation','<b>'.$lang['invalid_email'].'</b><meta http-equiv=refresh content="3;url=?p=account&sub=manage">');
	}
}

//	************************************************************
// Change Pass. Buffer function for the SDL

function changePass()
{
	global $lang, $user, $Account;
	$newpass = trim($_POST['new_pass']);
	if(strlen($newpass) > 3)
	{
		if($Account->setPassword($user['id'], $newpass) == TRUE)
		{
			output_message('success', $lang['change_pass_success'].'</b><meta http-equiv=refresh content="4;url=?p=account&sub=manage">');
		}
		else
		{
			output_message('error', '<b>Change Password Failed! Please contact an Administrator');
		}
	}
	else
	{
		output_message('error','<b>'.$lang['change_pass_short'].'</b><meta http-equiv=refresh content="4;url=?p=account&sub=manage">');
	}
}

//	************************************************************
// Change secret questions, Buffer function for the SDL

function changeSQ()
{
	global $user, $lang, $Account;
	$change = $Account->setSecretQuestions($user['id'], $_POST['secretq1'], $_POST['secreta1'], $_POST['secretq2'], $_POST['secreta2']);
	if($change == 1)
	{
		output_message('success','<b>'.$lang['changed_secretq'].'</b><meta http-equiv=refresh content="4;url=?p=account&sub=manage">');
	}
	elseif($change == 2)
	{
		output_message('error','<b>'.$lang['secretq_error_same'].'</b><meta http-equiv=refresh content="3;url=?p=account&sub=manage">');
	}
	elseif($change == 3)
	{
		output_message('error','<b>'.$lang['secretq_error_short'].'</b><meta http-equiv=refresh content="3;url=?p=account&sub=manage">');
	}
	else
	{
		output_message('error','<b>'.$lang['secretq_error_symbols'].'</b><meta http-equiv=refresh content="3;url=?p=account&sub=manage">');
	}
}

//	************************************************************
// Reset secret questions

function resetSQ()
{
	global $user, $lang, $Account;
	if($Account->resetSecretQuestions($user['id']) == TRUE)
	{
		output_message('success','<b>'.$lang['reset_secretq_success'].' Please wait while you are redirected...
			</b><meta http-equiv=refresh content="4;url=?p=account&sub=manage">');
	}
}

//	************************************************************
// Main Detail changing function

function changeDetails()
{
	global $lang, $user, $Account;
	
	$success = 0;
	
	// If password isnt emtpy
	if(!empty($_POST['new_pass']))
	{
		$change = changePass();
		if($change == TRUE)
		{
			$success++;
		}
	}
	else
	{
		$success++;
	}
	
	$setemail = changeEmail();
	if($setemail == TRUE)
	{
		$success++;
	}
	
	$setexp = $Account->setExpansion($user['id'], $_POST['exp']);
	if($setexp == TRUE)
	{
		$success++;
	}
	
	if($success == 3)
	{
		output_message('success', $lang['account_update_success'].'<meta http-equiv=refresh content="4;url=?p=account&sub=manage">');
	}
}
?>