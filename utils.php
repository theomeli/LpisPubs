<?php

	function printContent($mediaType, $typeToPrint){	
		include('connection.php');
		
		mysql_query("SET NAMES 'utf8'");
		mysql_query("SET CHARACTER SET 'utf8'");
		$query = "SELECT * FROM `Publications` WHERE MediaType = '" . $mediaType . "' ORDER BY `PublicationYear` DESC";                                    	
	       
		$results = mysql_query($query);
               $num_results = mysql_num_rows($results);
		
		echo '<table class="t09" style="margin:8px">     
			<tr>
				<td colspan="2"><h3>&nbsp;' . $typeToPrint . ' : ' . $num_results . ' articles</h3></td>
			</tr>';
		
			$currentYear = 0; //initialization of $currentYear to insert the if block below

			for ($i=0; $i<$num_results; $i++){
				$resultRow = mysql_fetch_array($results);
				if ($resultRow['PublicationYear'] !== $currentYear){ 	
					$currentYear = $resultRow['PublicationYear'];
						echo '<tr>
							<td colspan="2" style="padding-top:13px; border-bottom:#4078CC thin dotted"><strong>&nbsp;Year ' . $currentYear . '</strong></td>
						</tr>';
				}
				
				$type = $resultRow['MediaType'];
				switch($type) {
					case 'Journal':
						$publicationTypeOf = 'JournalArticle';
						$ontologyType = 'biblio';
						break;
					case 'Workshop Proceedings':
						$publicationTypeOf = 'ConferenceArticle';
						$ontologyType = 'biblio';
						break;
					case 'Conference Proceedings':
						$publicationTypeOf = 'ConferenceArticle';
						$ontologyType = 'biblio';
						break;
					case 'Book':
						$publicationTypeOf = 'Book';
						$ontologyType = 'bibo';
						break;
					case 'Book Chapter':
						$publicationTypeOf = 'Chapter';
						$ontologyType = 'bibo';
						break;
					case 'Technical Report':
						$publicationTypeOf = 'Report';
						$ontologyType = 'bibo';
						break;
					default:
						$publicationTypeOf = 'Document';
						$ontologyType = 'bibo';
				}


				echo '<tr>
					<td style="text-align:center; vertical-align:top">
						<img src="images2006/DIAM_OR.GIF" alt="379" style="margin-right:5px;"> 
					</td>
					<td>
						<div about="http://lpis.csd.auth.gr/lod/publications#pubID' . $resultRow['PublicationID'] . '" typeof="' . $ontologyType . ':' . $publicationTypeOf . '"><p style="text-align: justify;">';
							$authorsNamesQuery = "SELECT * FROM `Authors` AS T1 INNER JOIN `Rights` AS T2 ON T1.AuthorID=T2.AuthorID INNER JOIN `Publications` AS T3 ON T2.PublicationID=T3.PublicationID WHERE T3.PublicationID= '" . $resultRow['PublicationID'] . "'"; 
							$authorsNames = mysql_query($authorsNamesQuery);
							$num_authorsNames = mysql_num_rows($authorsNames);
							
							for ($j = 0; $j < $num_authorsNames; $j++){
								$authorName = mysql_fetch_array($authorsNames);							
								echo '<span>';
								
									if ($authorName[1] > '')
										echo $authorName[1] . '. ';
									if ($authorName[2] > '')
										echo $authorName[2] . '. '; 
								echo $authorName[3] . '</span>, ';
								
							}
							
							
							$authorsNames = mysql_query($authorsNamesQuery);
							$num_authorsNames = mysql_num_rows($authorsNames);
							echo '<span rel="dc:creator" typeOf="rdf:Seq">';
								for ($j = 0; $j < $num_authorsNames; $j++){
									$authorName = mysql_fetch_array($authorsNames);
									$k = $j + 1;
									echo '<span property="rdf:_' . $k . '" href="http://lpis.csd.auth.gr/lod/publications#authorID' . $authorName[0] . '" ></span>';
								}
							echo '</span>';
							
							
							$authorsNames = mysql_query($authorsNamesQuery);
							$num_authorsNames = mysql_num_rows($authorsNames);
							for ($j = 0; $j < $num_authorsNames; $j++){	
								$authorName = mysql_fetch_array($authorsNames);							
								echo '<span about="http://lpis.csd.auth.gr/lod/publications#authorID' . $authorName[0] . '" typeof="foaf:Person">';
									if ($authorName[1] > '')
										echo '<span property="foaf:givenName" content="' . $authorName[1] . '"></span>';
									echo '<span property="foaf:familyName" content="' . $authorName[3] . '"></span>
								</span>';
							}
							
							
							$publicationQuery = "SELECT PublicationTitle, MediaTitle, MediaPublisher, MediaVolInfo, PublicationLocation, PublicationPagesInMedium, PublicationYear FROM `Publications` WHERE PublicationID='" . $resultRow['PublicationID'] . "'";
							$publicationR = mysql_query($publicationQuery);
							$publication = mysql_fetch_array($publicationR);
							echo '"<span property="dc:title">'
								. $publication[0] .
							'</span>", ';
						
								
							if ($publication[1] != 'N/A' && $publication[1]!='Book' && $publication[1]!='…' && $authorName[2]>'')
								echo '<span property="bibo:presentedAt">' .
									$publication[1] . 
								'</span>, ';
							if ($publication[2] != 'N/A' && $publication[2]>'')	
								echo '<span property="dct:publisher">' . 
									$publication[2] . 
								'</span>, ';
							if ($publication[3] != 'N/A' && $publication[3]>'')
								echo '<span property="bibo:volume">' . 
									$publication[3] . 
								'</span>, ';
							if ($publication[4] != 'N/A' && $publication[4]>'')
								echo '<span property="dct:location">' . 
									$publication[4] . 
								'</span>, ';
							if ($publication[5]!='N/A' && $publication[1]>'')
								echo 'pp. <span property="bibo:pages">' . 
									$publication[5] . 
								'</span>, ';
							echo '<span property="dc:date">' . 
								$publication[6] . 
							'</span>
							
							<a class="link08" href="http://lpis.csd.auth.gr/paper_details.asp?publicationID=' . $resultRow['PublicationID'] . '"><img src="images2006/btn-frwd.gif" alt=">>" width="17" height="15"  title="paper details..." style="vertical-align:bottom"></a></p>';
							
							$publicationRefs = "SELECT COUNT(*) FROM `References` WHERE RefPaperID=" . $resultRow['PublicationID'];
							$numOfRefsRaw = mysql_query($publicationRefs);
							$numOfRefs = mysql_fetch_array($numOfRefsRaw);
							
							echo '<span rel="biblio:hasTotalCitations" typeOf="c4o:GlobalCitationCount">
								<span property="c4o:hasGlobalCountValue" content="' . $numOfRefs[0] . '" datatype="xsd:nonNegativeInteger"></span>	
							</span>';
						echo '</div>';
						
					
					echo '</td>
					<td style="vertical-align: top; width: 38px; text-align: right;">';
						$publicationRefs = "SELECT COUNT(*) FROM `References` WHERE RefPaperID=" . $resultRow['PublicationID'];
						$numOfRefsRaw = mysql_query($publicationRefs);
						$numOfRefs = mysql_fetch_array($numOfRefsRaw);
						if ($numOfRefs[0] > 0)
							echo '<p style="text-align: right; display: inline; font-size: 11px; color: #FF3300; padding: 0px;">ref: <span>' .
								$numOfRefs[0] .
							'</span></p>
					</td>
					
				</tr>';
			}
		
		echo '</table>';
		
		if(isset($_POST['Username'])){
        	$username = $_POST['Username'];
        	$username = addslashes($username);
        	$username = mysql_real_escape_string($username);
    	}
    	if(isset($_POST['Password'])){
        	$password = $_POST['Password'];
        	$password = addslashes($password);
        	$password = mysql_real_escape_string($password);
    	}
 
		mysql_close($link);
	}
?>
