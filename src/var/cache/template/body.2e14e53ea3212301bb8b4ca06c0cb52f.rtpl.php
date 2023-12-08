<?php if(!class_exists('Rain\Tpl')){exit;}?><?php require $this->checkTemplate('test/'.$headerTemplate);?> <?php $counter1=-1;  if( isset($videos) && ( is_array($videos) || $videos instanceof Traversable ) && sizeof($videos) ) foreach( $videos as $key1 => $value1 ){ $counter1++; ?>

<div id="wrapper" name="<?php echo $value1["id"]; ?>">
    <!-- start filelist/file -->
    <form action="search.php" method="post" id="formId">
        <div class="blueTable">
            <div class="row">
                <div class="col blueTable-th blueTable-thead blueTable-thead-th text-center">
                    <!-- start filelist/popup_js -->
                    <span onclick="popup('/plex/videoinfo.php?id=<?php echo $value1["id"]; ?>', 'videoinfo')"><?php echo $value1["title"]; ?></span>
                    <!-- end filelist/popup_js -->
                </div>
            </div>
            <div class="row flex-nowrap">
                <!-- start filelist/file_vertical -->
                <div class="col blueTable-th blueTable-thead blueTable-thead-th" style="max-width: 35px">
                    <div class="vertical-text">&nbsp;&nbsp;&nbsp;<?php echo $value1["rownum"]; ?> of <?php echo $value1["rownum"]; ?></div>
                </div>
                <!-- end filelist/file_vertical -->

                <!-- start filelist/file_thumbnail -->
                <div class="col" style="max-width: 340px">
                    <img
                        src="http://10.0.0.101/plex<?php echo $value1["thumbnail"]; ?>"
                        onclick="popup('http://10.0.0.101/plex/video.php?id=<?php echo $value1["id"]; ?>', 'videoplayer')"
                        onerror="this.onerror=null;this.src='http://10.0.0.101/plex/images/default.jpg'"
                        class="position-relative" />
                </div>
                <!-- end filelist/file_thumbnail -->

                <div class="col">
                    <!-- start videoinfo/file_row -->
                    <div class="row blueTable-tr-even mt-2">
                        <div class="col-2 blueTable-tbody-td">
                            <span class="text-dark fs-5"> Title</span>
                        </div>
                        <div class="col-9 blueTable-tbody-td fs-5"><?php echo $value1["title"]; ?></div>
                    </div>
                    <?php if( is_array($value1["artist"]) ){ ?> <?php $oldval = $value1; ?> <?php $tag = 'artist'; ?> <?php $buttonValue = $value1["artist"]; ?> <?php require $this->checkTemplate("test/button");?> <?php $value1 = $oldval; ?> <?php } ?>

                    <?php if( is_array($value1["genre"]) ){ ?> <?php $oldval = $value1; ?> <?php $tag = 'genre'; ?> <?php $buttonValue = $value1["genre"]; ?> <?php require $this->checkTemplate("test/button");?> <?php $value1 = $oldval; ?> <?php } ?>

                    <?php if( is_array($value1["studio"]) ){ ?> <?php $oldval = $value1; ?> <?php $tag = 'studio'; ?> <?php $buttonValue = $value1["studio"]; ?> <?php require $this->checkTemplate("test/button");?> <?php $value1 = $oldval; ?> <?php } ?>

                    <?php if( is_array($value1["keyword"]) ){ ?> <?php $oldval = $value1; ?> <?php $tag = 'keyword'; ?> <?php $buttonValue = $value1["keyword"]; ?> <?php require $this->checkTemplate("test/button");?> <?php $value1 = $oldval; ?> <?php } ?>


                    <div class="row mt-2">
                        <div class="col-2 blueTable-tbody-td">
                            <span class="text-dark fs-5"> Filename</span>
                        </div>
                        <div class="col-9 blueTable-tbody-td fs-5"><?php echo $value1["filename"]; ?></div>
                    </div>
                    <div class="row blueTable-tr-even mt-2">
                        <div class="col-2 blueTable-tbody-td">
                            <span class="text-dark fs-5"> Fullpath</span>
                        </div>
                        <div class="col-9 blueTable-tbody-td fs-5"><?php echo $value1["fullpath"]; ?></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-2 blueTable-tbody-td">
                            <span class="text-dark fs-5"> Duration</span>
                        </div>
                        <div class="col-9 blueTable-tbody-td fs-5"><?php echo $value1["duration"]; ?></div>
                    </div>
                    <div class="row blueTable-tr-even mt-2">
                        <div class="col-2 blueTable-tbody-td">
                            <span class="text-dark fs-5"> Filesize</span>
                        </div>
                        <div class="col-9 blueTable-tbody-td fs-5"><?php echo $value1["filesize"]; ?><span class="fs-0-8 bold">GB</span></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-2 blueTable-tbody-td">
                            <span class="text-dark fs-5"> Added</span>
                        </div>
                        <div class="col-9 blueTable-tbody-td fs-5"><?php echo $value1["added"]; ?></div>
                    </div>
                    <div class="row blueTable-tr-even mt-2">
                        <div class="col-2 blueTable-tbody-td">
                            <span class="text-dark fs-5"> </span>
                        </div>
                        <div class="col-9 blueTable-tbody-td fs-5">
                            <!-- start filelist/file_videoinfo -->
                            <div class="row">
                                <div class="col-3">Format</div>
                                <div class="col-3">BitRate</div>
                                <div class="col-2">Width</div>
                                <div class="col-2">Height</div>
                            </div>
                            <div class="row">
                                <div class="col-3">MPEG-4</div>
                                <div class="col-3">9.56 MB</div>
                                <div class="col-2">4096</div>
                                <div class="col-2">2160</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php } ?> <?php require $this->checkTemplate('test/'.$footerTemplate);?>

