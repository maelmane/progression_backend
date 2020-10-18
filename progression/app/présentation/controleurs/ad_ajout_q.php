<?php

require_once 'prog.php';
include( 'admin.php' );

function page_contenu(){
	$infos=récupérer_paramètres();
	
    if( isset( $_POST[ 'submit' ] )){
        sauvegarder();
    }
	
    render_page( $infos );
}

function récupérer_paramètres(){
	$infos=array();

	$infos=array_merge( $infos, récupérer_thèmes( isset( $_GET[ "theme" ] ) ? $_GET[ "theme" ] : 0 ));

	if( isset( $_GET[ "theme" ] ) && $_GET[ "theme" ]>0 ){
		$infos=array_merge( $infos, récupérer_séries( new Theme( $_GET[ "theme" ] ), isset( $_GET[ "serie" ] ) ? $_GET[ "serie" ] : 0 ));
	}
	if( isset( $_GET[ "serie" ] ) && $_GET[ "serie" ]>0 ){
		$infos=array_merge( $infos, récupérer_questions( new Serie( $_GET[ "serie" ] ), isset( $_GET[ "question" ] ) ? $_GET[ "question" ] : 0 ));
		$infos=array_merge( $infos, récupérer_question_sélectionnée( new Question( $_GET[ "question" ] ), isset( $_GET[ "question" ] ) ?  $_GET[ "question" ] : 0 ));
	}
	
	return $infos;
}

function récupérer_thèmes( $id_thème ){
	$infos=array();
	
	$infos[ "thèmes" ]=get_themes();
	foreach( $infos[ "thèmes" ] as $thème ){
		$thème->sélectionné=$thème->id==$id_thème;
	}
	$infos[ "nouveau_thème" ]=$id_thème==0;

	return $infos;
}

function récupérer_séries( $thème, $id_série ){
	$infos=array();
	
	$infos[ "séries" ]=$thème->get_series();
	foreach( $infos[ "séries" ] as $série ){
		$série->sélectionnée=$série->id==$id_série;
	}
	if( $id_série==0 ){
		$infos[ "nouvelle_série" ]=true;
	}

	return $infos;
}

function récupérer_questions( $série, $id_question ){
	$infos=array();
	
	$infos[ "questions" ]=$série->get_questions();
	foreach( $infos[ "questions" ] as $question ){
		$question->sélectionnée=$question->id==$id_question;
		$infos[ "mode" ]=get_mode( $question->lang );
	}
	if( $id_question==0 ){
		$infos[ "nouvelle_question" ]=true;
	}

	return $infos;
}

function récupérer_question_sélectionnée( $question, $id_question ){
	if( $id_question==0 ){
		$question=new QuestionProg( 0 );
	}
	elseif( $question->type==Question::TYPE_PROG ){
		$question=new QuestionProg( $question->id );
		$question->type_prog=true;
	}
	elseif( $question->type==Question::TYPE_BD ){
		$question=new QuestionBD( $question->id );		
		$question->type_bd=true;
	}
	elseif( $question->type==Question::TYPE_SYS ){
		$question=new QuestionSysteme( $question->id );
		$question->type_sys=true;
	}

	$question->première_ligne_éditeur_precode=compter_lignes( $question->pre_exec )+1;
    $question->première_ligne_éditeur_incode=compter_lignes( $question->pre_exec )+compter_lignes( $question->pre_code )+1;

	return array( "question"=>$question );
}

function sauvegarder(){
	if( isset( $_POST[ 'theme' ] ) && $_POST[ 'theme' ]==0 ){
		$theme=new Theme( 0 );
		$theme->titre=$_POST[ 'theme_titre' ];
		$theme->save();
		header( "Location: index.php?p=ad_ajout_q&theme=$_POST[ theme ]" );
	}
	elseif( isset( $_POST[ 'theme' ] ) && isset( $_POST[ 'serie' ] ) &&$_POST[ 'serie' ]==0 ){
		$serie=new Serie( 0 );
		$serie->titre=$_POST[ 'serie_titre' ];
		$serie->themeID=$_POST[ 'theme' ];
		$serie->save();
		header( "Location: index.php?p=ad_ajout_q&theme=$_POST[ theme ]&serie=$_POST[ serie ]" );
	}
	//Sauvegarde
	elseif( isset( $_POST[ 'theme' ] ) && isset( $_POST[ 'serie' ] ) && isset( $_POST[ 'question' ] )){

		if( $_POST[ 'type' ]==Question::TYPE_PROG ){
			$qst=new QuestionProg( $_POST[ 'question' ] );
			$qst->actif=$_POST[ 'actif' ];
			$qst->type=Question::TYPE_PROG;
			$qst->serieID =$_GET[ 'serie' ];
			$qst->numero =$_POST[ 'numero' ];
			$qst->titre =$_POST[ 'titre' ];
			$qst->description =$_POST[ 'description' ];
			$qst->enonce =$_POST[ 'enonce' ];
			$qst->solution =$_POST[ 'solution' ];
			$qst->code_validation =$_POST[ 'code_validation' ];
			$qst->langid =$_POST[ 'langid' ];
			$qst->setup =$_POST[ 'setup' ];
			$qst->pre_exec =$_POST[ 'pre_exec' ];
			$qst->pre_code =$_POST[ 'pre_code' ];
			$qst->incode =$_POST[ 'incode' ];
			$qst->post_code =$_POST[ 'post_code' ];
			$qst->params =$_POST[ 'params' ];
			$qst->stdin =$_POST[ 'stdin' ];
			
			$qid=$qst->save();
			header( "Location: index.php?p=ad_ajout_q&theme=$_GET[ theme ]&serie=$_GET[ serie ]&question=$qid" );
		}
		elseif( $_POST[ 'type' ]==Question::TYPE_SYS ){
			$qst=new QuestionSysteme( $_POST[ 'question' ] );
			$qst->actif=$_POST[ 'actif' ];
			$qst->type=Question::TYPE_SYS;
			$qst->serieID = $_GET[ 'serie' ];
			$qst->numero=$_POST[ 'numero' ];
			$qst->titre=$_POST[ 'titre' ];
			$qst->description=$_POST[ 'description' ];
			$qst->enonce=$_POST[ 'enonce' ];
			$qst->solution_courte=$_POST[ 'solution_courte' ];
			$qst->code_validation=$_POST[ 'code_validation' ];
			$qst->image=$_POST[ 'image' ];
			$qst->user=$_POST[ 'username' ];
			$qst->verification=$_POST[ 'verification' ];
			
			$qid=$qst->save();
			header( "Location: index.php?p=ad_ajout_q&theme=$_GET[ theme ]&serie=$_GET[ serie ]&question=$qid" );
		}
		elseif( $_POST[ 'type' ]==Question::TYPE_BD ){
			
			$qst=new QuestionBD( $_POST[ 'question' ] );
			$qst->actif=$_POST[ 'actif' ];
			$qst->type=Question::TYPE_BD;
			$qst->serieID =$_GET[ 'serie' ];
			$qst->numero =$_POST[ 'numero' ];
			$qst->titre =$_POST[ 'titre' ];
			$qst->description =$_POST[ 'description' ];
			$qst->enonce =$_POST[ 'enonce' ];
			$qst->solution =$_POST[ 'solution' ];
			$qst->solution_courte =$_POST[ 'solution_courte' ];        
			$qst->code_validation =$_POST[ 'code_validation' ];
			$qst->langid =$_POST[ 'langid' ];
			$qst->setup =$_POST[ 'setup' ];
			$qst->pre_exec =$_POST[ 'pre_exec' ];
			$qst->pre_code =$_POST[ 'pre_code' ];
			$qst->incode =$_POST[ 'incode' ];
			$qst->post_code =$_POST[ 'post_code' ];
			$qst->params =$_POST[ 'params' ];
			$qst->stdin =$_POST[ 'stdin' ];

			$qst->image=$_POST[ 'image' ];
			$qst->user=$_POST[ 'username' ];
			$qst->verification=$_POST[ 'verification' ];
			
			$qid=$qst->save();
			header( "Location: index.php?p=ad_ajout_q&theme=$_GET[ theme ]&serie=$_GET[ serie ]&question=$qid" );
		}
	}
}

function compter_lignes( $texte ){
    if( $texte=="" ){
        return 0;
    }
    else{
        return count( preg_split( '/\n/',$texte ));
    }
}

function render_page( $infos ){
	$template=$GLOBALS[ 'mustache' ]->loadTemplate( "ad_ajout_q" );
	echo $template->render( $infos );
}

?>
