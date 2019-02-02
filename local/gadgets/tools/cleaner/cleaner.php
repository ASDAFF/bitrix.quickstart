<?
	/*
		Bitrix Cleaner v2.1 - https://github.com/creadome/bitrixcleaner
		Быстрая очистка 1С-Битрикс 		 				

		(c) 2015 Станислав Васильев - http://creado.me
		creadome@gmail.com
	*/

	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
	
	if ($USER->IsAdmin()) {
		$list = array(
			'/bitrix/cache/' => 'Неуправляемый кеш',
			'/bitrix/managed_cache/' => 'Управляемый кеш',
			'/upload/resize_cache/' => 'Миниатюры изображений'
		);

		if ($clean = $_GET['clean']) {
			if ($clean == 'all') { foreach ($list as $directory => $content) DeleteDirFilesEx($directory); } 
			else { if ($list[$clean]) DeleteDirFilesEx($clean); }
		}
				
		function countsize($directory) {		
			$count = array('file' => 0, 'size' => 0);
		
			foreach (scandir($directory) as $file) {
				if ($file != '.' && $file != '..') {
					if (is_dir($directory.$file)) {
						$inner = countsize($directory.$file.'/');

						$count['file'] += $inner['file']; 
						$count['size'] += $inner['size']; 
					} else {
						$count['file'] ++;
						$count['size'] += filesize($directory.$file); 
					}
				}
			}
			
			return $count;	
		}
?>

	<table>
		<tr>
			<th>Содержимое</th>
			<th>Путь</th>
			<th>Файлы</th>
			<th>Размер</th>
			<th></th>
		</tr>

		<? 
			foreach ($list as $directory => $content) { 
				$count = countsize($_SERVER['DOCUMENT_ROOT'].$directory);
				$count['size'] = round($count['size'] / 1048576, 2);
		?>	

			<tr>
				<td><?=$content?></td>
				<td><a href="/bitrix/admin/fileman_admin.php?lang=ru&amp;path=<?=$directory?>"><?=$directory?></a></td>
				<td><?=$count['file']?></td>
				<td><?=$count['size']?> Мб</td>

				<td class="clean">
					<span class="action-clean" data-clean="<?=$directory?>">Очистить</span>
				</td>
			</tr>

		<?
			} 
		?>
	</table>

	<input type="button" value="Очистить все" class="adm-btn-save action-clean" data-clean="all">

<?
	}
?>