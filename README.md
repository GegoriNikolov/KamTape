# leaked by aesthetiful
fuck you jr and all of the kamtape community, i am tired of supporting this shit site that is down 24/7. join [EraCast](https://www.eracast.cc/) my site is actually better
# KamTape.com
A near 1:1 evolving recreation of YouTube from 2005 from all of the Flickr photos, videos, tutorials, and books I could gather. Hosted at kamtape.com.
## Requirements
The site won't work without these. Specifically user interactive functions and uploading.
| Needed                 	| Why                                                                                                                    	|
|------------------------	|------------------------------------------------------------------------------------------------------------------------	|
| Composer               	| Handles most of the other required things here.                                                                        	|
| SoftCreatR\MimeDetetor 	| Detects video in uploader.                                                                                             	|
| PHPmailer              	| Sends e-mails from KamTape.                                                                                            	|
| SMTP Server             | Welcome, Invite Friends, sending messages won't work without this. You can just use gmail-- i think they support smtp   |
| 512 MB request limit   	| Similar configuration to what KamTape has :^)                                                                          	|
| CDN                    	| You'll need the kamtape/cdn repository for video uploading to work. Some patching will be required after that however. 	|
| PHP Short Tags Support 	| Programming Habits. :3                                                                                             	    |
_____________________________________________________________________________________________________________________________________________________
You'll need to patch the source code at /my_videos_upload_post.php to whatever you put the kamtape cdn at. How you do that is not my problem.
If you have any issues with setting up the service for development, let me know and I can provide you with the normal beta subdomain and make changes to it as you push to the GitHub.
