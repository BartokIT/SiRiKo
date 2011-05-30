<?php
/**
* $action variabile che contiene il nome dell'area corrente
*
*/

	add_to_debug("Azione",  $action);
	switch ($action)
	{
		default:
		case "":
			//Reindirizzo alla parte che gestisce un nuovo giocatore
			return new ReturnedArea("game","default","default" ,"add_partecipant");
			break;
		case "init_order_gamers":
			
		case "pass_to_next_gamer":
			$curr_stat = get_current_turn_and_action(session_id());
			if (count($curr_stat))
			{
				//Possibile passare al prossimo giocatore solo se sono il gamer corrente
				if (session_id() == $curr_stat["user"])
				{
					$next_gamer = get_next_gamer($curr_stat["id_game"], $curr_stat["current_participant"]);
					print "Next gamer " . 	$next_gamer;
				}
			}
			return new ReturnedPage("default.php");						
			break;
		case "play_game":
			echo "Playing...";

			$curr_stat = get_current_turn_and_action(session_id());
			if (count($curr_stat))
			{
				if (session_id() == $curr_stat["user"])
				{
					echo "Player corrente";
				}
				else
				{
					echo "In attesa di giocare";
				}
			}	
			
			return new ReturnedPage("default.php");
			break;
		case "add_partecipant":
			//Controllo se il giocatore è già registrato
			$id_game = is_in_game(session_id());
			
			//Se già è in sessione allora lo rimando alla pagina di init
			if ($id_game != -1 )
			{
				return new ReturnedArea("game", "init");
			}
			
			$games = get_id_games();
			//Se esiste già una partita allora lo aggiungo a questa
			if (count($games))
			{
				$unique_game = $games[0];
				if ($id_game == -1)
					insert_user_in_game(session_id(), $unique_game);		
			}
			else
			{//altrimenti ne creo una nuova
				insert_user_in_game(session_id());
			}
			
			//Lo rimando alla pagina di inizio del gioco	
			return new ReturnedArea("game", "init");
			break;
	}


?>
