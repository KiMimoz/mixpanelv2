<?php
if(!this::getSpec("panel_settings","Maintenance","ID",1))
{
	return user::redirect_to("");
}
?>

<div class="mainArea-content">
    <div class="container">
        <div id="mainArea-body">
            <div id="mainArea-content-body">
                <div class="indexRow row" id="index-ongoing-matches">
                    <div class="index-title">
                        <h1>Site under Maintenance</h1>
                    </div>
                    <div class="indexRow-inner row">
                    	We will come back as soon as possible!!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>