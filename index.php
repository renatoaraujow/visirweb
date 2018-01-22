<?php
header('Content-Type: text/html; charset=utf-8');

//error_reporting(0);
$ponteiro = fopen ("visir.txt", "r");

$linhas = [];

while (!feof ($ponteiro))
    $linhas[] = fgets($ponteiro, 4096);

$registros = [];
$dataHoraRegistroAtual = "";
$idRegistro = 0;
$linhasCopia = $linhas;
$registroAberto = false;
foreach ($linhas as $numeroLinha => $linha) {
	//pega data e hora
    if(strpos($linha, "proto_http") !== false && strpos($linhas[$numeroLinha-1], "proto_http") === false)
        $dataHoraRegistroAtual = substr($linha, 1, 19) . "----" . $numeroLinha;

	//inicia um registro
	if(strpos($linha, "<protocol") !== false) {
		if($registroAberto) {
			$idRegistro = $idRegistro+1;
			unset($registros[$idRegistro]['circuitlist']);
			unset($registros[$idRegistro]['functiongenerator']);
			unset($registros[$idRegistro]['oscilloscope']);
		}

		$registros[$idRegistro]['id'] = $idRegistro;
		$registros[$idRegistro]['dataHora'] = $dataHoraRegistroAtual;
		$registroAberto = true;
	}

	//pega chave da sessao (a chave é gerada a cada acesso)
    if(strpos($linha, "sessionkey") !== false)
		$registros[$idRegistro]['key'] = substr($linha, strpos($linha, "sessionkey")+12, 32);

	//pega lista de circuitos
    if(strpos($linha, "<circuitlist") !== false) {
		$registros[$idRegistro]['circuitlist'][] = substr($linha, strpos($linha, "<circuitlist")+13, strlen($linha)-strpos($linha, "<circuitlist>")+13);
		$break = false;
		foreach($linhasCopia as $numeroLinhaCircuito => $linhaCircuito){
			if($numeroLinhaCircuito > $numeroLinha) {
				if(strpos($linhaCircuito, "</circuitlist>") !== false) {
					$break = true;
				}
				$registros[$idRegistro]['circuitlist'][] = substr($linhaCircuito, 0, strlen($linhaCircuito));
			}

			if($break) {
				array_pop($registros[$idRegistro]['circuitlist']);
				break;
			}
		}
	}

	$abreFechaMesmaLinha = false;
	if(strpos($linha, "<functiongenerator") !== false) {
		if(strpos($linha, "</functiongenerator>") !== false) {
			$abreFechaMesmaLinha = true;
		}

		$registros[$idRegistro]['functiongenerator']['fg_waveform']      = "";
		$registros[$idRegistro]['functiongenerator']['fg_frequency']     = "";
		$registros[$idRegistro]['functiongenerator']['fg_amplitude']     = "";
		$registros[$idRegistro]['functiongenerator']['fg_offset']        = "";
		$registros[$idRegistro]['functiongenerator']['fg_startphase']    = "";
		$registros[$idRegistro]['functiongenerator']['fg_triggermode']   = "";
		$registros[$idRegistro]['functiongenerator']['fg_triggersource'] = "";
		$registros[$idRegistro]['functiongenerator']['fg_burstcount']    = "";
		$registros[$idRegistro]['functiongenerator']['fg_dutycycle']     = "";

		$break = false;
		foreach($linhasCopia as $numeroLinhaFG => $linhaFG){
			if($numeroLinhaFG >= $numeroLinha) {
				if(strpos($linhaFG, "</functiongenerator>") !== false)
					$break = true;

				if(strpos($linhaFG, "id=") !== false)
					$registros[$idRegistro]['functiongenerator']['id'] = substr($linhaFG, strpos($linhaFG, "id=")+4, 1);

				if($abreFechaMesmaLinha) {

					$mLinhaFG = explode("<", $linhaFG);
					foreach($mLinhaFG as $vLinhaFG) {
						if(strpos($vLinhaFG, "fg_waveform") !== false && strpos($vLinhaFG, "/fg_waveform>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_waveform'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_waveform")+strlen("fg_waveform")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_waveform")+strlen("fg_waveform")+8) - 2);

						if(strpos($vLinhaFG, "fg_frequency") !== false && strpos($vLinhaFG, "/fg_frequency>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_frequency'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_frequency")+strlen("fg_frequency")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_frequency")+strlen("fg_frequency")+8) - 2);

						if(strpos($vLinhaFG, "fg_amplitude") !== false && strpos($vLinhaFG, "/fg_amplitude>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_amplitude'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_amplitude")+strlen("fg_amplitude")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_amplitude")+strlen("fg_amplitude")+8) - 2);

						if(strpos($vLinhaFG, "fg_offset") !== false && strpos($vLinhaFG, "/fg_offset>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_offset'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_offset")+strlen("fg_offset")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_offset")+strlen("fg_offset")+8) - 2);

						if(strpos($vLinhaFG, "fg_startphase") !== false && strpos($vLinhaFG, "/fg_startphase>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_startphase'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_startphase")+strlen("fg_startphase")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_startphase")+strlen("fg_startphase")+8) - 2);

						if(strpos($vLinhaFG, "fg_triggermode") !== false && strpos($vLinhaFG, "/fg_triggermode>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_triggermode'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_triggermode")+strlen("fg_triggermode")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_triggermode")+strlen("fg_triggermode")+8) - 2);

						if(strpos($vLinhaFG, "fg_triggersource") !== false && strpos($vLinhaFG, "/fg_triggersource>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_triggersource'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_triggersource")+strlen("fg_triggersource")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_triggersource")+strlen("fg_triggersource")+8) - 2);

						if(strpos($vLinhaFG, "fg_burstcount") !== false && strpos($vLinhaFG, "/fg_burstcount>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_burstcount'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_burstcount")+strlen("fg_burstcount")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_burstcount")+strlen("fg_burstcount")+8) - 2);

						if(strpos($vLinhaFG, "fg_dutycycle") !== false && strpos($vLinhaFG, "/fg_dutycycle>") === false)
							$registros[$idRegistro]['functiongenerator']['fg_dutycycle'] = substr($vLinhaFG, strpos($vLinhaFG, "fg_dutycycle")+strlen("fg_dutycycle")+8, strlen($vLinhaFG) - (strpos($vLinhaFG, "fg_dutycycle")+strlen("fg_dutycycle")+8) - 2);
					}

				} else {

					if(strpos($linhaFG, "fg_waveform") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_waveform'] = substr($linhaFG, strpos($linhaFG, "fg_waveform")+strlen("fg_waveform")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_waveform")+strlen("fg_waveform")+8) - 5);

						if(strpos($linhaFG, "fg_frequency") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_frequency'] = substr($linhaFG, strpos($linhaFG, "fg_frequency")+strlen("fg_frequency")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_frequency")+strlen("fg_frequency")+8) - 5);

						if(strpos($linhaFG, "fg_amplitude") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_amplitude'] = substr($linhaFG, strpos($linhaFG, "fg_amplitude")+strlen("fg_amplitude")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_amplitude")+strlen("fg_amplitude")+8) - 5);

						if(strpos($linhaFG, "fg_offset") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_offset'] = substr($linhaFG, strpos($linhaFG, "fg_offset")+strlen("fg_offset")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_offset")+strlen("fg_offset")+8) - 5);

						if(strpos($linhaFG, "fg_startphase") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_startphase'] = substr($linhaFG, strpos($linhaFG, "fg_startphase")+strlen("fg_startphase")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_startphase")+strlen("fg_startphase")+8) - 5);

						if(strpos($linhaFG, "fg_triggermode") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_triggermode'] = substr($linhaFG, strpos($linhaFG, "fg_triggermode")+strlen("fg_triggermode")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_triggermode")+strlen("fg_triggermode")+8) - 5);

						if(strpos($linhaFG, "fg_triggersource") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_triggersource'] = substr($linhaFG, strpos($linhaFG, "fg_triggersource")+strlen("fg_triggersource")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_triggersource")+strlen("fg_triggersource")+8) - 5);

						if(strpos($linhaFG, "fg_burstcount") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_burstcount'] = substr($linhaFG, strpos($linhaFG, "fg_burstcount")+strlen("fg_burstcount")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_burstcount")+strlen("fg_burstcount")+8) - 5);

						if(strpos($linhaFG, "fg_dutycycle") !== false)
							$registros[$idRegistro]['functiongenerator']['fg_dutycycle'] = substr($linhaFG, strpos($linhaFG, "fg_dutycycle")+strlen("fg_dutycycle")+8, strlen($linhaFG) - (strpos($linhaFG, "fg_dutycycle")+strlen("fg_dutycycle")+8) - 5);
				}

			}

			if($break)
				break;
		}
	}










	$abreFechaMesmaLinha = false;
	if(strpos($linha, "<oscilloscope") !== false) {
		if(strpos($linha, "</oscilloscope>") !== false) {
			$abreFechaMesmaLinha = true;
		}

		$registros[$idRegistro]['oscilloscope']['osc_autoscale'] 					= "";
		$registros[$idRegistro]['oscilloscope']['horizontal']['horz_samplerate'] 	= "";
		$registros[$idRegistro]['oscilloscope']['horizontal']['horz_refpos']     	= "";
		$registros[$idRegistro]['oscilloscope']['horizontal']['horz_recordlength']  = "";
		$registros[$idRegistro]['oscilloscope']['trigger']['trig_source'] 			= "";
		$registros[$idRegistro]['oscilloscope']['trigger']['trig_slope'] 			= "";
		$registros[$idRegistro]['oscilloscope']['trigger']['trig_coupling'] 		= "";
		$registros[$idRegistro]['oscilloscope']['trigger']['trig_level'] 			= "";
		$registros[$idRegistro]['oscilloscope']['trigger']['trig_mode'] 			= "";
		$registros[$idRegistro]['oscilloscope']['trigger']['trig_delay'] 			= "";
		$registros[$idRegistro]['oscilloscope']['trigger']['trig_received'] 		= "";

		$break = false;
		foreach($linhasCopia as $numeroLinhaOS => $linhaOS){
			if($numeroLinhaOS >= $numeroLinha) {
				if(strpos($linhaOS, "</oscilloscope>") !== false)
					$break = true;

				if(strpos($linhaOS, "id=") !== false)
					$registros[$idRegistro]['oscilloscope']['id'] = substr($linhaOS, strpos($linhaOS, "id=")+4, 1);

				if($abreFechaMesmaLinha) {

					$mLinhaOS = explode("<", $linhaOS);
					foreach($mLinhaOS as $vLinhaOS) {
						if(strpos($vLinhaOS, "osc_autoscale") !== false && strpos($vLinhaOS, "/osc_autoscale>") === false)
							$registros[$idRegistro]['oscilloscope']['osc_autoscale'] = substr($vLinhaOS, strpos($vLinhaOS, "osc_autoscale")+strlen("osc_autoscale")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "osc_autoscale")+strlen("osc_autoscale")+8) - 2);

						if(strpos($vLinhaOS, "horz_samplerate") !== false && strpos($vLinhaOS, "/horz_samplerate>") === false)
							$registros[$idRegistro]['oscilloscope']['horizontal']['horz_samplerate'] = substr($vLinhaOS, strpos($vLinhaOS, "horz_samplerate")+strlen("horz_samplerate")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "horz_samplerate")+strlen("horz_samplerate")+8) - 2);

						if(strpos($vLinhaOS, "horz_refpos") !== false && strpos($vLinhaOS, "/horz_refpos>") === false)
							$registros[$idRegistro]['oscilloscope']['horizontal']['horz_refpos'] = substr($vLinhaOS, strpos($vLinhaOS, "horz_refpos")+strlen("horz_refpos")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "horz_refpos")+strlen("horz_refpos")+8) - 2);

						if(strpos($vLinhaOS, "horz_recordlength") !== false && strpos($vLinhaOS, "/horz_recordlength>") === false)
							$registros[$idRegistro]['oscilloscope']['horizontal']['horz_recordlength'] = substr($vLinhaOS, strpos($vLinhaOS, "horz_recordlength")+strlen("horz_recordlength")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "horz_recordlength")+strlen("horz_recordlength")+8) - 2);



						//channels
						if(strpos($linha, "<channels") !== false) {
							if(strpos($linha, "</channels>") !== false) {
								$abreFechaMesmaLinha = true;
							}

							$registros[$idRegistro]['oscilloscope']['osc_autoscale'] 					= "";
							$registros[$idRegistro]['oscilloscope']['horizontal']['horz_samplerate'] 	= "";
							$registros[$idRegistro]['oscilloscope']['horizontal']['horz_refpos']     	= "";
							$registros[$idRegistro]['oscilloscope']['horizontal']['horz_recordlength']  = "";
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_source'] 			= "";
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_slope'] 			= "";
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_coupling'] 		= "";
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_level'] 			= "";
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_mode'] 			= "";
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_delay'] 			= "";
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_received'] 		= "";

							$break = false;
							foreach($linhasCopia as $numeroLinhaOS => $linhaOS){
								if($numeroLinhaOS >= $numeroLinha) {
									if(strpos($linhaOS, "</oscilloscope>") !== false)
										$break = true;

									if(strpos($linhaOS, "id=") !== false)
										$registros[$idRegistro]['oscilloscope']['id'] = substr($linhaOS, strpos($linhaOS, "id=")+4, 1);

									if($abreFechaMesmaLinha) {

										$mLinhaOS = explode("<", $linhaOS);
										foreach($mLinhaOS as $vLinhaOS) {
											if(strpos($vLinhaOS, "osc_autoscale") !== false && strpos($vLinhaOS, "/osc_autoscale>") === false)
												$registros[$idRegistro]['oscilloscope']['osc_autoscale'] = substr($vLinhaOS, strpos($vLinhaOS, "osc_autoscale")+strlen("osc_autoscale")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "osc_autoscale")+strlen("osc_autoscale")+8) - 2);

											if(strpos($vLinhaOS, "horz_samplerate") !== false && strpos($vLinhaOS, "/horz_samplerate>") === false)
												$registros[$idRegistro]['oscilloscope']['horizontal']['horz_samplerate'] = substr($vLinhaOS, strpos($vLinhaOS, "horz_samplerate")+strlen("horz_samplerate")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "horz_samplerate")+strlen("horz_samplerate")+8) - 2);

											if(strpos($vLinhaOS, "horz_refpos") !== false && strpos($vLinhaOS, "/horz_refpos>") === false)
												$registros[$idRegistro]['oscilloscope']['horizontal']['horz_refpos'] = substr($vLinhaOS, strpos($vLinhaOS, "horz_refpos")+strlen("horz_refpos")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "horz_refpos")+strlen("horz_refpos")+8) - 2);

											if(strpos($vLinhaOS, "horz_recordlength") !== false && strpos($vLinhaOS, "/horz_recordlength>") === false)
												$registros[$idRegistro]['oscilloscope']['horizontal']['horz_recordlength'] = substr($vLinhaOS, strpos($vLinhaOS, "horz_recordlength")+strlen("horz_recordlength")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "horz_recordlength")+strlen("horz_recordlength")+8) - 2);



											if(strpos($vLinhaOS, "trig_source") !== false && strpos($vLinhaOS, "/trig_source>") === false)
												$registros[$idRegistro]['oscilloscope']['trigger']['trig_source'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_source")+strlen("trig_source")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_source")+strlen("trig_source")+8) - 2);

											if(strpos($vLinhaOS, "trig_slope") !== false && strpos($vLinhaOS, "/trig_slope>") === false)
												$registros[$idRegistro]['oscilloscope']['trigger']['trig_slope'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_slope")+strlen("trig_slope")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_slope")+strlen("trig_slope")+8) - 2);

											if(strpos($vLinhaOS, "trig_coupling") !== false && strpos($vLinhaOS, "/trig_coupling>") === false)
												$registros[$idRegistro]['oscilloscope']['trigger']['trig_coupling'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_coupling")+strlen("trig_coupling")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_coupling")+strlen("trig_coupling")+8) - 2);

											if(strpos($vLinhaOS, "trig_level") !== false && strpos($vLinhaOS, "/trig_level>") === false)
												$registros[$idRegistro]['oscilloscope']['trigger']['trig_level'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_level")+strlen("trig_level")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_level")+strlen("trig_level")+8) - 2);

											if(strpos($vLinhaOS, "trig_mode") !== false && strpos($vLinhaOS, "/trig_mode>") === false)
												$registros[$idRegistro]['oscilloscope']['trigger']['trig_mode'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_mode")+strlen("trig_mode")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_mode")+strlen("trig_mode")+8) - 2);

											if(strpos($vLinhaOS, "trig_delay") !== false && strpos($vLinhaOS, "/trig_delay>") === false)
												$registros[$idRegistro]['oscilloscope']['trigger']['trig_delay'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_delay")+strlen("trig_delay")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_delay")+strlen("trig_delay")+8) - 2);

											if(strpos($vLinhaOS, "trig_received") !== false && strpos($vLinhaOS, "/trig_received>") === false)
												$registros[$idRegistro]['oscilloscope']['trigger']['trig_received'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_received")+strlen("trig_received")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_received")+strlen("trig_received")+8) - 2);

											if(strpos($vLinhaOS, "OS_dutycycle") !== false && strpos($vLinhaOS, "/OS_dutycycle>") === false)
												$registros[$idRegistro]['oscilloscope']['OS_dutycycle'] = substr($vLinhaOS, strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8) - 2);

											if(strpos($vLinhaOS, "OS_dutycycle") !== false && strpos($vLinhaOS, "/OS_dutycycle>") === false)
												$registros[$idRegistro]['oscilloscope']['OS_dutycycle'] = substr($vLinhaOS, strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8) - 2);
										}

									} else {

										if(strpos($linhaOS, "osc_autoscale") !== false && strpos($linhaOS, "/osc_autoscale>") === false)
												$registros[$idRegistro]['oscilloscope']['osc_autoscale'] = substr($linhaOS, strpos($linhaOS, "osc_autoscale")+strlen("osc_autoscale")+8, strlen($linhaOS) - (strpos($linhaOS, "osc_autoscale")+strlen("osc_autoscale")+8) - 5);

										if(strpos($linhaOS, "horz_samplerate") !== false && strpos($linhaOS, "/horz_samplerate>") === false)
											$registros[$idRegistro]['oscilloscope']['horizontal']['horz_samplerate'] = substr($linhaOS, strpos($linhaOS, "horz_samplerate")+strlen("horz_samplerate")+8, strlen($linhaOS) - (strpos($linhaOS, "horz_samplerate")+strlen("horz_samplerate")+8) - 5);

										if(strpos($linhaOS, "horz_refpos") !== false && strpos($linhaOS, "/horz_refpos>") === false)
											$registros[$idRegistro]['oscilloscope']['horizontal']['horz_refpos'] = substr($linhaOS, strpos($linhaOS, "horz_refpos")+strlen("horz_refpos")+8, strlen($linhaOS) - (strpos($linhaOS, "horz_refpos")+strlen("horz_refpos")+8) - 5);

										if(strpos($linhaOS, "horz_recordlength") !== false && strpos($linhaOS, "/horz_recordlength>") === false)
											$registros[$idRegistro]['oscilloscope']['horizontal']['horz_recordlength'] = substr($linhaOS, strpos($linhaOS, "horz_recordlength")+strlen("horz_recordlength")+8, strlen($linhaOS) - (strpos($linhaOS, "horz_recordlength")+strlen("horz_recordlength")+8) - 5);



										if(strpos($linhaOS, "trig_source") !== false && strpos($linhaOS, "/trig_source>") === false)
											$registros[$idRegistro]['oscilloscope']['trigger']['trig_source'] = substr($linhaOS, strpos($linhaOS, "trig_source")+strlen("trig_source")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_source")+strlen("trig_source")+8) - 5);

										if(strpos($linhaOS, "trig_slope") !== false && strpos($linhaOS, "/trig_slope>") === false)
											$registros[$idRegistro]['oscilloscope']['trigger']['trig_slope'] = substr($linhaOS, strpos($linhaOS, "trig_slope")+strlen("trig_slope")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_slope")+strlen("trig_slope")+8) - 5);

										if(strpos($linhaOS, "trig_coupling") !== false && strpos($linhaOS, "/trig_coupling>") === false)
											$registros[$idRegistro]['oscilloscope']['trigger']['trig_coupling'] = substr($linhaOS, strpos($linhaOS, "trig_coupling")+strlen("trig_coupling")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_coupling")+strlen("trig_coupling")+8) - 5);

										if(strpos($linhaOS, "trig_level") !== false && strpos($linhaOS, "/trig_level>") === false)
											$registros[$idRegistro]['oscilloscope']['trigger']['trig_level'] = substr($linhaOS, strpos($linhaOS, "trig_level")+strlen("trig_level")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_level")+strlen("trig_level")+8) - 5);

										if(strpos($linhaOS, "trig_mode") !== false && strpos($linhaOS, "/trig_mode>") === false)
											$registros[$idRegistro]['oscilloscope']['trigger']['trig_mode'] = substr($linhaOS, strpos($linhaOS, "trig_mode")+strlen("trig_mode")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_mode")+strlen("trig_mode")+8) - 5);

										if(strpos($linhaOS, "trig_delay") !== false && strpos($linhaOS, "/trig_delay>") === false)
											$registros[$idRegistro]['oscilloscope']['trigger']['trig_delay'] = substr($linhaOS, strpos($linhaOS, "trig_delay")+strlen("trig_delay")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_delay")+strlen("trig_delay")+8) - 5);

										if(strpos($linhaOS, "trig_received") !== false && strpos($linhaOS, "/trig_received>") === false)
											$registros[$idRegistro]['oscilloscope']['trigger']['trig_received'] = substr($linhaOS, strpos($linhaOS, "trig_received")+strlen("trig_received")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_received")+strlen("trig_received")+8) - 5);


									}

								}







						//channels


						if(strpos($vLinhaOS, "trig_source") !== false && strpos($vLinhaOS, "/trig_source>") === false)
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_source'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_source")+strlen("trig_source")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_source")+strlen("trig_source")+8) - 2);

						if(strpos($vLinhaOS, "trig_slope") !== false && strpos($vLinhaOS, "/trig_slope>") === false)
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_slope'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_slope")+strlen("trig_slope")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_slope")+strlen("trig_slope")+8) - 2);

						if(strpos($vLinhaOS, "trig_coupling") !== false && strpos($vLinhaOS, "/trig_coupling>") === false)
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_coupling'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_coupling")+strlen("trig_coupling")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_coupling")+strlen("trig_coupling")+8) - 2);

						if(strpos($vLinhaOS, "trig_level") !== false && strpos($vLinhaOS, "/trig_level>") === false)
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_level'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_level")+strlen("trig_level")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_level")+strlen("trig_level")+8) - 2);

						if(strpos($vLinhaOS, "trig_mode") !== false && strpos($vLinhaOS, "/trig_mode>") === false)
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_mode'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_mode")+strlen("trig_mode")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_mode")+strlen("trig_mode")+8) - 2);

						if(strpos($vLinhaOS, "trig_delay") !== false && strpos($vLinhaOS, "/trig_delay>") === false)
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_delay'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_delay")+strlen("trig_delay")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_delay")+strlen("trig_delay")+8) - 2);

						if(strpos($vLinhaOS, "trig_received") !== false && strpos($vLinhaOS, "/trig_received>") === false)
							$registros[$idRegistro]['oscilloscope']['trigger']['trig_received'] = substr($vLinhaOS, strpos($vLinhaOS, "trig_received")+strlen("trig_received")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "trig_received")+strlen("trig_received")+8) - 2);

						if(strpos($vLinhaOS, "OS_dutycycle") !== false && strpos($vLinhaOS, "/OS_dutycycle>") === false)
							$registros[$idRegistro]['oscilloscope']['OS_dutycycle'] = substr($vLinhaOS, strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8) - 2);

						if(strpos($vLinhaOS, "OS_dutycycle") !== false && strpos($vLinhaOS, "/OS_dutycycle>") === false)
							$registros[$idRegistro]['oscilloscope']['OS_dutycycle'] = substr($vLinhaOS, strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8, strlen($vLinhaOS) - (strpos($vLinhaOS, "OS_dutycycle")+strlen("OS_dutycycle")+8) - 2);
					}

				} else {

					if(strpos($linhaOS, "osc_autoscale") !== false && strpos($linhaOS, "/osc_autoscale>") === false)
							$registros[$idRegistro]['oscilloscope']['osc_autoscale'] = substr($linhaOS, strpos($linhaOS, "osc_autoscale")+strlen("osc_autoscale")+8, strlen($linhaOS) - (strpos($linhaOS, "osc_autoscale")+strlen("osc_autoscale")+8) - 5);

					if(strpos($linhaOS, "horz_samplerate") !== false && strpos($linhaOS, "/horz_samplerate>") === false)
						$registros[$idRegistro]['oscilloscope']['horizontal']['horz_samplerate'] = substr($linhaOS, strpos($linhaOS, "horz_samplerate")+strlen("horz_samplerate")+8, strlen($linhaOS) - (strpos($linhaOS, "horz_samplerate")+strlen("horz_samplerate")+8) - 5);

					if(strpos($linhaOS, "horz_refpos") !== false && strpos($linhaOS, "/horz_refpos>") === false)
						$registros[$idRegistro]['oscilloscope']['horizontal']['horz_refpos'] = substr($linhaOS, strpos($linhaOS, "horz_refpos")+strlen("horz_refpos")+8, strlen($linhaOS) - (strpos($linhaOS, "horz_refpos")+strlen("horz_refpos")+8) - 5);

					if(strpos($linhaOS, "horz_recordlength") !== false && strpos($linhaOS, "/horz_recordlength>") === false)
						$registros[$idRegistro]['oscilloscope']['horizontal']['horz_recordlength'] = substr($linhaOS, strpos($linhaOS, "horz_recordlength")+strlen("horz_recordlength")+8, strlen($linhaOS) - (strpos($linhaOS, "horz_recordlength")+strlen("horz_recordlength")+8) - 5);



					if(strpos($linhaOS, "trig_source") !== false && strpos($linhaOS, "/trig_source>") === false)
						$registros[$idRegistro]['oscilloscope']['trigger']['trig_source'] = substr($linhaOS, strpos($linhaOS, "trig_source")+strlen("trig_source")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_source")+strlen("trig_source")+8) - 5);

					if(strpos($linhaOS, "trig_slope") !== false && strpos($linhaOS, "/trig_slope>") === false)
						$registros[$idRegistro]['oscilloscope']['trigger']['trig_slope'] = substr($linhaOS, strpos($linhaOS, "trig_slope")+strlen("trig_slope")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_slope")+strlen("trig_slope")+8) - 5);

					if(strpos($linhaOS, "trig_coupling") !== false && strpos($linhaOS, "/trig_coupling>") === false)
						$registros[$idRegistro]['oscilloscope']['trigger']['trig_coupling'] = substr($linhaOS, strpos($linhaOS, "trig_coupling")+strlen("trig_coupling")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_coupling")+strlen("trig_coupling")+8) - 5);

					if(strpos($linhaOS, "trig_level") !== false && strpos($linhaOS, "/trig_level>") === false)
						$registros[$idRegistro]['oscilloscope']['trigger']['trig_level'] = substr($linhaOS, strpos($linhaOS, "trig_level")+strlen("trig_level")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_level")+strlen("trig_level")+8) - 5);

					if(strpos($linhaOS, "trig_mode") !== false && strpos($linhaOS, "/trig_mode>") === false)
						$registros[$idRegistro]['oscilloscope']['trigger']['trig_mode'] = substr($linhaOS, strpos($linhaOS, "trig_mode")+strlen("trig_mode")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_mode")+strlen("trig_mode")+8) - 5);

					if(strpos($linhaOS, "trig_delay") !== false && strpos($linhaOS, "/trig_delay>") === false)
						$registros[$idRegistro]['oscilloscope']['trigger']['trig_delay'] = substr($linhaOS, strpos($linhaOS, "trig_delay")+strlen("trig_delay")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_delay")+strlen("trig_delay")+8) - 5);

					if(strpos($linhaOS, "trig_received") !== false && strpos($linhaOS, "/trig_received>") === false)
						$registros[$idRegistro]['oscilloscope']['trigger']['trig_received'] = substr($linhaOS, strpos($linhaOS, "trig_received")+strlen("trig_received")+8, strlen($linhaOS) - (strpos($linhaOS, "trig_received")+strlen("trig_received")+8) - 5);


				}

			}

			if($break)
				break;
		}
	}








	//finaliza um registro
	if(strpos($linha, "</protocol") !== false) {
		$idRegistro = $idRegistro+1;
		unset($registros[$idRegistro]['circuitlist']);
		unset($registros[$idRegistro]['functiongenerator']);
		unset($registros[$idRegistro]['oscilloscope']);
		$registroAberto = false;
	}
}

fclose ($ponteiro);

echo "<pre>"; print_r($registros);
?>
