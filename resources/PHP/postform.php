
<div id="float">
	Post something on your timeline...
	<form name="profile-post" action="<?php echo $host;?>post/" method="post" onsubmit="return Social.checkTwig('timelinepost-float');" autocomplete="off" data-lpignore="true" enctype="multipart/form-data">
	 <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="123" />
			<input type="hidden" name="csrf" value="<?php echo $csrf;?>" />
			<textarea name="post-message" cols="25" rows="10" id="timelinepost-float" placeholder="Post a message..." onkeydown="Social.timelinePost('timelinepost-float','charcounter-float');" /></textarea>
			<div id="progress-charcount"></div><div><div id="charcount"><span id="charcounter-float">255</span> characters left.</div>
			<div id="emoji"></div>
			<!-- <span id="spellcheck">Spellcheck <input type="checkbox" value="" id="spellcheck-input" /></span> -->
			</div>
			<label for="mixedmedia"><img id="mixedmedia-image" src="../resources/images/icons/file.png" alt=".gif, .jpg, .png, .ogg or .mp3. Maximum of 20MB." title=".gif, .jpg, .png, .ogg or .mp3. Maximum of 20MB.">
			<input type="file" id="mixedmedia" name="mixedmedia[]" onchange="Social.fileUploads('mixedmedia-image', this.value,'postform-button');" accept="image/png, image/jpeg, image/gif, image/jpg, audio/mp3, audio/ogg" style="display:none;"/>
			</label>  
			<img src="<?php echo $host;?>resources/images/icons/emoji.png" width="30" id="emoji-button2" title="Pick emoji?" alt="Pick emoji?" onclick="Emojis.emoticons('timelinepost-float')"/> <input type="submit" id="postform-button" name="post" value="Post"/>
	</form>
</div>
