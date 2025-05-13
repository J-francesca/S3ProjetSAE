<?php

foreach ($tab_gt as $gt){
	$idTheme = $gt->getIdTheme();
	$theme = Theme::getThemeById($idTheme);
	
	?>
	<option value="<?php echo $idTheme; ?>"><?php echo $theme->getNomTheme();?> </option>
	<?php
}

?>