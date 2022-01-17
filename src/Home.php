
<?php
  $generalConnection = "";
  $baseLinkPHP = 'C:\xampp\htdocs\src';
  $targetPage = ""; /* = navButton */
  $fileAbout = ""; /* About.txt */
  $featured[] = array(); /* Numerical Indexes */
  $featuredTitlesArray[] = array(); /* String Indexes */
  $featuredTitlesArray["title"] = "";
  $featuredTitlesArray["drawing"] = "";
  $featuredTitlesArray["music"] = "";
  $featuredTitlesArray["content"] = "";
  $drawing[] = array();
  $drawingOutput = "";
  $writing[] = array(); /* List of Files {.txt/.docx} */
  $targetWritingFile = ""; /* = wL */
  $writingOutput = "";
  $music[] = array(); /* List of Files {.m4a} */
  $musicOutput = "";
  
  function fileRead($directory, $file) { // Returns contents of target file in target directory.
    global $generalConnection;
    global $baseLinkPHP;
    $contents = "";
    
    if($directory == null) { // If the directory is Final Project.
      chdir($baseLinkPHP);
    }
    else {
      chdir($baseLinkPHP . "/$directory");
    }
    $generalConnection = fopen($file, "r");
    
    while(!feof($generalConnection)) {
      $contents .= fgets($generalConnection) . "<br/>";
    }
    return $contents;
  }

  function fillTitles($directory, &$targetArray) { // Fills target array with the titles of files in target directory. Search begins in directory 'Final Project'. 
    global $generalConnection;
    global $baseLinkPHP;
    $currentFile = "";

    chdir($baseLinkPHP);
    $generalConnection = opendir($directory);
    $targetArray = array(); // Reset array.
    
    while($currentFile !== false) { // Fills array with titles. 
      $currentFile = readDir($generalConnection);
      $targetArray[] = $currentFile;
    }
  }
  
  function setFeaturedVariables() { // Assign $featuredTitlesArray[].
    global $generalConnection;
    global $baseLinkPHP;
    global $featured;
    global $featuredTitlesArray;
    
    fillTitles("Featured", $featured);
    foreach($featured as $file) {
      if(preg_match("/.txt$/", $file)) { // Assign "title" & "content"
        $featuredTitlesArray["title"] = "$file";
        $featuredTitlesArray["content"] = fileRead("Featured", $file);
      }
      else if(preg_match("/.png$/", $file) or preg_match("/.jpg$/", $file)) { // Assign "drawing"
        $featuredTitlesArray["drawing"] = "$file";
      }
      else if(preg_match("/.m4a$/", $file)) { // Assign "music"
        $featuredTitlesArray["music"] = "$file";
      }
    }
  }
  
  function initializeContent() { // Page: Home/Writing/Drawing/Music
    global $targetPage;
    global $targetWritingFile;
    
    $targetPage = filter_input(INPUT_GET, "navButton");
    $targetWritingFile = filter_input(INPUT_GET, "wL");
    if($targetPage == "Writing" or isset($targetWritingFile)) {
      initializeWriting();
    }
    else if ($targetPage == "Drawing") {
      initializeDrawing();
    }
    else if ($targetPage == "Music") {
      initializeMusic();
    }
    else { /* $targetPage = "Home" or $targetPage = "" */
      initializeHome();
    }
  }
  
  function initializeHome() { // Page: Home
    global $featuredTitlesArray;
    global $fileAbout;
    $title = "";
    $drawingCaption = "";
    
    setFeaturedVariables();
    $fileAbout = fileRead("", "About.txt");
    $title = substr($featuredTitlesArray["title"], 0, -4); // Eve.txt => Eve
    $drawingCaption = substr($featuredTitlesArray["drawing"], 0, -4); // Violet.jpg => Violet
    
    print <<<HERE
    <div id = "home" class = "content">
      <h1>$title</h1>
      <div>
        <div id = "L">
          <img src = "Featured\\{$featuredTitlesArray["drawing"]}", alt = "alternate text"/>
          <p>$drawingCaption</p>
        </div>
        <div id = "R">
          <audio controls = "controls">
            <source src = "Featured\\{$featuredTitlesArray["music"]}">
            alternate text
          </audio>    
          <p>{$featuredTitlesArray["content"]}</p>
        </div>
      </div>
      <h1>About this Website</h1>
      <p>$fileAbout</p>
    </div>
HERE;
  }
  
  function initializeWriting() { // Page: Writing
    global $writingOutput;
    global $targetWritingFile;
    
    if(isset($targetWritingFile)) {
      readWritingFile();
    }
    else {
      readWriting();
    }
    print <<<HERE
    <div id = "writing" class = "content">
      <h1>Writing</h1>
      <div>
        $writingOutput
      </div>
    </div>
HERE;
  }
  
  function readWriting() { // Set $writingOutput
    global $writing;
    global $writingOutput;
    $title = ""; /* Eve.txt => Eve */
    
    fillTitles("Writing", $writing);
    $writingOutput .= "<form action = \"\"
                             method = \"get\">\n";
    foreach($writing as $file) {
      if (preg_match("/.txt$/", $file)) {
        $title = substr($file, 0, -4); /* Assumes (4) */ 
        $writingOutput .= "<button type = \"submit\"
                                   name = \"wL\"
                                   value = \"$file\">$title</button>";
      }
      else if(preg_match("/.docx$/", $file)) {
        $title = substr($file, 0, -5); /* Assumes (5) */ 
        $writingOutput .= "<button type = \"submit\"
                                   name = \"wL\"
                                   value = \"$file\">$title</button>";
      }
    }
    $writingOutput .= "</form>\n";
  }
  
  function readWritingFile() { // Set $writingOutput, note: Word Files cannot be read(symbols)
    global $targetWritingFile;
    global $writingOutput;
    
    $writingOutput .= fileRead("Writing", $targetWritingFile);
  }
  
  function initializeDrawing() { // Page: Drawing
    global $drawingOutput;
    
    readImages();
    print <<<HERE
    <div id = "drawing" class = "content">
      <h1>Drawing</h1>
      <div>
        $drawingOutput
      </div>
    </div>
HERE;
  }
  
  function readImages() { // Set $drawingOutput
    global $drawing;
    global $drawingOutput;
    $caption = ""; /* Violet.jpg => Violet */
    
    fillTitles("Drawing", $drawing);
    foreach($drawing as $file) {
      if (preg_match("/.jpg$/", $file) or preg_match("/.png$/", $file)) {
        $caption = substr($file, 0, -4); /* Assume (4) {.jpg/.png} */
        $drawingOutput .= "<span>\n";
        $drawingOutput .= "<img src = \"Drawing\\$file\"
                                alt = 'Alternate Text'>\n";
        $drawingOutput .= "<p>$caption</p>";
        $drawingOutput .= "</span>\n";
      }
    }
  }
  
  function initializeMusic() { // Page: Music
    global $musicOutput;
    
    readMusic();
    print <<<HERE
    <div id = "music" class = "content">
      <h1>Music</h1>
      <div>
        $musicOutput
      </div>
    </div>
HERE;
  }
  
  function readMusic() { // Set $musicOutput
    global $music;
    global $musicOutput;
    $title = ""; /* FRH.m4a = > FRH */
    
    fillTitles("Music", $music);
    foreach($music as $file) {
      if(preg_match("/.m4a$/", $file)) {
        $title = substr($file, 0, -4); /* Assume (4) */
        $musicOutput .= "<div>\n";
        $musicOutput .= "<label>$title</label>";
        $musicOutput .= "<audio controls = \"controls\">\n";
        $musicOutput .= "<source src = \"Music\\$file\"
                                 alt = \"alternate text\">\n";
        $musicOutput .= "</audio>\n";
        $musicOutput .= "</div>\n";
      }
    }
  }
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset = "UTF-8">
  <title>Home</title>
  <link rel = "stylesheet", type = "text/css", href = "Home.css"/>
</head>
<body>
  <div id = "page">
    <form id = "nav"
          action = ""
          method = "get">
      <span>
        <span id = "glow"></span>
        <button type = "submit" name = "navButton" value = "Home">Home</button>
        <button type = "submit" name = "navButton" value = "Writing">Writing</button>
        <button type = "submit" name = "navButton" value = "Drawing">Drawing</button>
        <button type = "submit" name = "navButton" value = "Music">Music</button>
      </span>
    </form>
    <?php
      initializeContent();
    ?>
  </div>
</body>
</html>