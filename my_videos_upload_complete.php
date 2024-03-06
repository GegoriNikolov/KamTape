<?php 
require "needed/start.php";

force_login();

$video = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video->execute([$_GET['v']]);

if($video->rowCount() == 0) {
	redirect("index.php");
	die();
} else {
	$video = $video->fetch(PDO::FETCH_ASSOC);
}

if($video['uid'] != $session['uid']){
    redirect("index.php");
}

?>
<div class="tableSubTitle">Thank You</div>
<span class="success">Your video was successfully added!</span>
<p>Your video is currently being processed and will be available to view in a few minutes.</p>
Want to upload more videos? <a href="/my_videos_upload.php">Click here</a>

<p>
<img src="/img/MoreOptionsTab.gif" />
<div class="tableSubTitle">Share your video (Optional):</div>
Use the share manager application below to easily share your video with friends, family, and other contacts.<br>If that's not your thing, you can copy and paste the video url (permalink) into an e-mail.
<table width="760" border="0" cellspacing="0" cellpadding="0">
    <tbody><tr>
      <td width="380" valign="middle">
        <div align="left"><iframe id="embedplayer" src="http://www.kamtape.com/v/<?php echo htmlspecialchars($_GET['v']); ?>" width="448" height="382" allowfullscreen="" scrolling="off" frameborder="0"></iframe></div></td>
      <td width="400" valign="top" align="center">
        <div style="font-size: 11px; font-weight: bold; color: #CC6600; padding: 5px 0px 5px 0px;">Video URL (Permalink): <span style="color: #000;">E-mail or link it!</span></div>
				<div style="font-size: 11px; padding-bottom: 15px;">
				<input name="video_link" type="text" onclick="javascript:document.linkForm.video_link.focus();document.linkForm.video_link.select();" value="https://kamtape.com/?v=<?php echo htmlspecialchars($_GET['v']); ?>" size="50" readonly="true" style="font-size: 10px; text-align: center;">

                <div style="font-size: 11px; font-weight: bold; color: #CC6600; padding: 5px 0px 5px 0px;">Embed your video: <span style="color: #000;">Put this video on your Web site!<br> Copy and paste the code below to embed the video.</span></div>
				<div style="font-size: 11px;">
                <textarea name="video_play" rows="4" cols="33" onclick="javascript:document.linkForm.video_play.focus();document.linkForm.video_play.select();" readonly="">&lt;iframe src=&quot;//www.kamtape.com/v/<?php echo htmlspecialchars($_GET['v']); ?>&quot; width=&quot;460&quot; height=&quot;357&quot; allowfullscreen scrolling=&quot;off&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;
</textarea><p>
                        <div align="left"> <span class="standoutLabel">Tip:</span> Put KamTape videos on <img src="/img/BloggerIcon.gif" width="61" height="21" align="absmiddle">&nbsp;&nbsp; <img src="/img/EbayIcon.gif" width="51" height="21">&nbsp;&nbsp; <img src="/img/DeSpaceIcon.gif" width="81" height="21"> </div><p><strong>To watch your video now, please <a href="/?v=<?php echo htmlspecialchars($_GET['v']); ?>">go here</a>.</strong>

 

				</div></td>
    </tr>
  </tbody></table>
  <p><div class="tableSubTitle">Date & Address Details</div>
	<div class="pageTable">
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td width="200" align="right"><span class="label">Date Recorded:</span><br><span class="formFieldInfo">(Optional)</span></td>
					<td><select name="addr_month" tabindex="13">
						<option value="---">---</option>
							<option value="1"> Jan  </option>
							<option value="2"> Feb  </option>
							<option value="3"> Mar  </option>
							<option value="4"> Apr  </option>
							<option value="5"> May  </option>
							<option value="6"> Jun  </option>
							<option value="7"> Jul  </option>
							<option value="8"> Aug  </option>
							<option value="9"> Sep  </option>
							<option value="10"> Oct  </option>
							<option value="11"> Nov  </option>
							<option value="12"> Dec  </option>
					</select>
					<select name="addr_day" tabindex="14">
						<option value="---">---</option>
							<option>1</option>
							<option>2</option>
							<option>3</option>
							<option>4</option>
							<option>5</option>
							<option>6</option>
							<option>7</option>
							<option>8</option>
							<option>9</option>
							<option>10</option>
							<option>11</option>
							<option>12</option>
							<option>13</option>
							<option>14</option>
							<option>15</option>
							<option>16</option>
							<option>17</option>
							<option>18</option>
							<option>19</option>
							<option>20</option>
							<option>21</option>
							<option>22</option>
							<option>23</option>
							<option>24</option>
							<option>25</option>
							<option>26</option>
							<option>27</option>
							<option>28</option>
							<option>29</option>
							<option>30</option>
							<option>31</option>
					</select>					
					<select name="addr_yr" tabindex="15">
						<option value="---">---</option>
								<option>2023</option>
								<option>2022</option>
								<option>2021</option>
								<option>2020</option>
								<option>2019</option>
								<option>2018</option>
								<option>2017</option>
								<option>2016</option>
								<option>2015</option>
								<option>2014</option>
								<option>2013</option>
								<option>2012</option>
								<option>2011</option>
								<option>2010</option>
								<option>2009</option>
								<option>2008</option>
								<option>2007</option>
								<option>2006</option>
								<option>2005</option>
								<option>2004</option>
								<option>2003</option>
								<option>2002</option>
								<option>2001</option>
								<option>2000</option>
								<option>1999</option>
								<option>1998</option>
								<option>1997</option>
								<option>1996</option>
								<option>1995</option>
								<option>1994</option>
								<option>1993</option>
								<option>1992</option>
								<option>1991</option>
								<option>1990</option>
								<option>1989</option>
								<option>1988</option>
								<option>1987</option>
								<option>1986</option>
								<option>1985</option>
								<option>1984</option>
								<option>1983</option>
								<option>1982</option>
								<option>1981</option>
								<option>1980</option>
								<option>1979</option>
								<option>1978</option>
								<option>1977</option>
								<option>1976</option>
								<option>1975</option>
								<option>1974</option>
								<option>1973</option>
								<option>1972</option>
								<option>1971</option>
								<option>1970</option>
								<option>1969</option>
								<option>1968</option>
								<option>1967</option>
								<option>1966</option>
								<option>1965</option>
								<option>1964</option>
								<option>1963</option>
								<option>1962</option>
								<option>1961</option>
								<option>1960</option>
								<option>1959</option>
								<option>1958</option>
								<option>1957</option>
								<option>1956</option>
								<option>1955</option>
								<option>1954</option>
								<option>1953</option>
								<option>1952</option>
								<option>1951</option>
								<option>1950</option>
								<option>1949</option>
								<option>1948</option>
								<option>1947</option>
								<option>1946</option>
								<option>1945</option>
								<option>1944</option>
								<option>1943</option>
								<option>1942</option>
								<option>1941</option>
								<option>1940</option>
								<option>1939</option>
								<option>1938</option>
								<option>1937</option>
								<option>1936</option>
								<option>1935</option>
								<option>1934</option>
								<option>1933</option>
								<option>1932</option>
								<option>1931</option>
								<option>1930</option>
								<option>1929</option>
								<option>1928</option>
								<option>1927</option>
								<option>1926</option>
								<option>1925</option>
								<option>1924</option>
								<option>1923</option>
								<option>1922</option>
								<option>1921</option>
								<option>1920</option>
								<option>1919</option>
								<option>1918</option>
								<option>1917</option>
								<option>1916</option>
								<option>1915</option>
								<option>1914</option>
								<option>1913</option>
								<option>1912</option>
								<option>1911</option>
								<option>1910</option>
								<option>1909</option>
								<option>1908</option>
								<option>1907</option>
								<option>1906</option>
								<option>1905</option>
								<option>1904</option>
								<option>1903</option>
								<option>1902</option>
								<option>1901</option>
								<option>1900</option>
					</select></td>
				</tr>
				<tr>
					<td width="200" align="right"><span class="label">Address Recorded:</span><br><span class="formFieldInfo">(Optional)</span></td>
					<td><input type="text" size="30" maxlength="160" name="field_upload_address" autocomplete="on"><br></td>
				</tr>
                </table><span style="margin-left: 214px" class="formFieldInfo">Examples: "165 University Ave, Palto Alto, CA" "New York City, NY" "Kyoto"</span><div class="pageTable">
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td width="200" align="right"><span class="label">Country:</span><br><span class="formFieldInfo">(Optional)</span></td>
					<td><?php echo '<select name="field_upload_country" tabindex="5">';
                        foreach ($_COUNTRIES as $code => $name) {
                        echo '<option>' . $name . '</option>';
                        }
                    echo '</select>';?></td>
                    
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="Continue ->"></td></form>
				</tr>
				<tr>
				</tr>
			</tbody>
		</table>
	</div>
</div>
                
<?php 
require "needed/end.php";
?>