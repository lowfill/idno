<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="span10 offset1">

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                ?>
                <label>
                    <span class="btn btn-primary btn-file">
                        <i class="icon-camera"></i> <span id="photo-filename">Take a photo</span> <input type="file" name="photo" id="photo" class="span9" accept="image/*;capture=camera" onchange="$('#photo-filename').html($(this).val())" />
                    </span>
                </label>
                <?php

                    }

                ?>
            </p>
            <p>
                <label>
                    Title:<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span9" />
                </label>
            </p>
            <p>
                <label>
                    Description<br />
                    <textarea name="body" id="body" class="span9 bodyInput mentionable"><?=htmlspecialchars($vars['object']->body)?></textarea>
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('image'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/photo/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>