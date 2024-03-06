<?php
require "../needed/start.php";
?>
<div class="tableSubTitle">Developer API: Error Codes</div>

<span class="apiHeader">1 : KamTape Internal Error</span><br>
<span class="apiDef">This is a potential issue with the KamTape API.  Please <a href="/contact.php">report the issue to us</a> using the subject "Developer Question."</span><br><br>

<span class="apiHeader">2 : Bad XML-RPC format parameter</span><br>
<span class="apiDef">The parameter passed to the XML-RPC API call was of an incorrect type.  Please see the <a href="dev_xmlrpc">XML-RPC interface documentation</a> for more details.</span><br><br>

<span class="apiHeader">3 : Unknown parameter specified</span><br>
<span class="apiDef">Please double-check that the specified parameters match those in the API reference.</span><br><br>

<span class="apiHeader">4 : Missing required parameter</span><br>
<span class="apiDef">Please double-check that all required parameters for the API method you're calling are present in your request.</span><br><br>

<span class="apiHeader">5 : No method specified</span><br>
<span class="apiDef">All API calls must specify a method name.</span><br><br>

<span class="apiHeader">6 : Unknown method specified</span><br>
<span class="apiDef">Please check that you've spelled the method name correctly.</span><br><br>

<span class="apiHeader">7 : Missing dev_id parameter</span><br>
<span class="apiDef">All requests must have a developer ID.  If you don't have one, please create a <a href="/my_profile_dev.php">developer profile</a>.</span><br><br>

<span class="apiHeader">8 : Bad or unknown dev_id specified</span><br>
<span class="apiDef">All requests must have a valid developer ID.  If you don't have one, please create a <a href="/my_profile_dev.php">developer profile</a>.</span><br><br>


		</td>
	</tr>
</table>
<?php
require "../needed/end.php";
?>