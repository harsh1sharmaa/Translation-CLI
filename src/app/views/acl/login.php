<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

<h2>hello world</h2>

    <h1>
        <?php echo $this->locale->_("hi") ?>
    </h1>
    <h1>
        <?php echo $this->locale->_("Contacts") ?>
    </h1>

    <form method='post'>
        <p>
            <label>
                <?php echo $this->locale->_("Last Name") ?>
            </label>

            <?php echo $form->render($this->locale->_('nameLast')); ?>
        </p>

        <p>
            <input type='submit' value=<?php $this->locale->_("Save") ?> />
        </p>
    </form>
</body>

</html>