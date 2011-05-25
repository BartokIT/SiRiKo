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
			$id_game = is_in_game(session_id());
			
			if ($id_game != -1 )
				return new ReturnedArea("game", "default", "default", "play_game");
			$games = get_id_games();
			if (count($games))
			{
				$unique_game = $games[0];
				if ($id_game == -1)
					insert_user_in_game(session_id(), $unique_game);		
			}
			else
			{
				insert_user_in_game(session_id());
			}
					
			$gamers=get_co_gamers(session_id());
			foreach ($gamers as $gamer)
			{
				print "Giocatore " .  $gamer . "<br/>";
			}
				
			return new ReturnedPage("default.php");		
			//return new ReturnedAjax("Test2");
			break;
	}


?>
