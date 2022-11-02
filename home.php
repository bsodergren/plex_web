<?php
DEFINE('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], ".php"));

require_once("_config.inc.php");

define('TITLE', "Home");

include __LAYOUT_HEADER__;
$db->where('hidden', 0);
$media_job = $db->withTotalCount()->get('media_job');
?>

<main role="main" class="container">
	<table class="blueTable">
		<thead>
			<tr>
				<th colspan=2>Media</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($db->totalCount > 0) {

				foreach ($media_job as $k => $v) {
					$url = __URL_HOME__ . "/form.php?job_id=" . $v['job_id'];
					$text = $v['job_number'] . " - " . $v['pdf_file'];

					$form = new Formr\Formr();
					$hidden = ["job_id" => $v['job_id']];
					$form->open("", '', __URL_HOME__ . "/action.php", 'post', '', $hidden);

			?>
					<tr id="RedHead">
						<td> <?php echo $text; ?> </td>
						<td align="right"><?php

											$form->checkbox(
												'hide',
												'Hide',
												'jobId_' . $v['job_id'],
												'jobId_' . $v['job_id'],
												"onchange=\"this.form.submit()\""
											);

											?>

						</td>
					<tr>
					<tr>
						<td colspan=2>
							<div class="container">
								<?php
								//draw_link($url,$text,'class="button"',false);



								$zip_file = APP_PATH . "/zip/" . basename($v['pdf_file'], ".pdf") . "_" . $v['job_number'] . ".zip";
								$xlsx_dir = APP_PATH . "/xlsx/" . basename($v['pdf_file'], ".pdf") . "_" . $v['job_number'] . "";


								$form->input_submit('actSubmit', '', "Process PDF Form", '', 'class="button"');

								if ($v["xlsx_dir"] && is_dir($xlsx_dir) == true) {
									$action = "View Forms";
									$form->input_submit('actSubmit', '', $action, '', 'class="button"');
								}



								if ($v["zip_file"] && file_exists($zip_file) == true) {
									$form->input_submit('actSubmit', '', 'Download Zip File', '', 'class="button"');
									$form->input_submit('actSubmit','','Mail Zip File','5','class="button"');

								}


								//	$form->close();



								//	$form_url = __URL_HOME__."/edit.php?job_id=".$v['job_id'];
								//	$edit_form = new Formr\Formr();
								//	$edit_form->open("",'',$form_url ,'post');

								// $edit_form = $form;

								if ($v['xlsx_dir'] == true) {
									$form->input_submit('actSubmit', '', 'delete_xlsx', '', 'class="button"');
								} else {
									$form->input_submit('actSubmit', '', 'create_xlsx', '', 'class="button"');
								}

								if ($v['zip_file'] == true) {
									$form->input_submit('actSubmit', '', 'delete_zip', '', 'class="button"');
								} elseif ($v['xlsx_dir'] == true) {
									$form->input_submit('actSubmit', '', 'create_zip', '', 'class="button"');
								}

								$form->input_submit('actSubmit', '', 'refresh_import', '', 'class="button"');
								$form->input_submit('actSubmit', '', 'delete_job', '', 'class="button"');
								$form->close();

								?>
							</div>
						</td>
					</tr>
				<?php  } ?>

			<?php

			}

			$db->where('hidden', 1);
			$media_job = $db->withTotalCount()->get('media_job');
			if ($db->totalCount > 0) {
			?>

				<tr>
					<td colspan=2>
						<div class="container">
							<p>
						</div>
					</td>
				</tr>

			<?php
				foreach ($media_job as $k => $v) {
					$form = new Formr\Formr();

					$hidden = ["job_id" => $v['job_id']];

					$form->open("HideForms", '', __URL_HOME__ . "/action.php", 'post', '', $hidden);
					$text = $v['job_number'] . " - " . $v['pdf_file'];
					echo '	<tr id="RedHead">';
					echo '<td> ' . $text . '</td><td align="right">';

					$form->checkbox(
						'show',
						'Show',
						'jobId_' . $v['job_id'],
						'jobId_' . $v['job_id'],
						"onchange=\"this.form.submit()\""
					);
					echo '</td>	</tr>';

					$form->close();
				}
			}
			?>
		</tbody>
	</table>
</main>
<?php include __LAYOUT_FOOTER__;  ?>