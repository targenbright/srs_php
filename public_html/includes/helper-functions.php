<?php

/**
 * Generates navbar elements based on user"s login status.
 */
function generateNavbarElements($loggedIn)
{
    global $navbarElements;

    if ($loggedIn) {
        $fields = $navbarElements["Logged In"];
    } else {
        $fields = $navbarElements["Logged Out"];
    }
    foreach ($fields as $k => $v) : ?>
        <li>
            <a target="<?= $v["target"] ?>" href="<?= $v["url"] ?>">
                <i class="<?= $v["icon"] ?>"></i>
                <span><?= $k ?></span>
            </a>
        </li>
    <?php
    endforeach;
}

/**
 * Displays results of a query as a table.
 */
function queryToTable($result)
{ if (!empty($result)) : ?>
    <table>
        <tr>
            <?php while ($fieldMetadata = $result->fetch_field()) : ?>
                <th><?= $fieldMetadata->name ?></th>
            <?php endwhile; ?>
        </tr>
        <?php while ($line = $result->fetch_assoc()) : ?>
            <tr>
                <?php foreach ($line as $cell) : ?>
                    <td> <?= $cell ?> </td>
                <?php endforeach; ?>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif;
}

/**
 * Displays results of a query as a table.
 */
function queryToSelectionTable($result)
{ if (!empty($result)) : ?>
    <table>
        <tr>
            <?php
            $i = 0;
            $idColumnArray = array();
            while ($fieldMetadata = $result->fetch_field()) :
                $fieldName = $fieldMetadata->name;
                if (strpos($fieldName, 'ID')) :
                    array_push($idColumnArray, $i) ?>
                    <th class="add-column"></th>
                <?php
                else :
                ?>
                    <th><?= $fieldMetadata->name ?></th>
                <?php endif; ?>
            <?php
                $i++;
            endwhile; ?>
        </tr>
        <?php
        while ($line = $result->fetch_assoc()) : ?>
            <tr>
                <?php
                $i = 0;
                foreach ($line as $cell) :
                    if (in_array($i, $idColumnArray)) : ?>
                        <td class="add-column"><input class="form-check-input" type="checkbox" name="courseId[]" value="<?= $cell ?>"></input></td>
                    <?php else : ?>
                        <td> <?= $cell ?> </td>
                    <?php endif;
                    $i++;
                    ?>
                <?php endforeach; ?>
            </tr>
        <?php
        endwhile; ?>
    </table>
    <?php endif;
}

function createNewConnection()
{
    global $server, $sqlUsername, $sqlPassword, $databaseName;
    $connection = new mysqli($server, $sqlUsername, $sqlPassword, $databaseName);

    return $connection;
}

function createFeedbackBanner($successMessage, $errorMessage)
{
    if ($errorMessage) : ?>
        <div class="alert alert-danger">
            <Strong>Error: </Strong> <?= $errorMessage; ?>
        </div>
    <?php
    else : ?>
        <div class="alert alert-success">
            <Strong>Success!</Strong> <?= $successMessage ?>
        </div>
    <?php
    endif; ?>
<?php
}

function spouseSearchToTable($result)
{ if (!empty($result)) : ?>
    <form action="" method="post" name="addSpouse" id="addSpouse">
        <table>
            <tr>
                <?php
                $i = 0;
                while ($fieldMetadata = $result->fetch_field()) :
                    if ($i === 0) {
                ?>
                        <th class="add-column"></th>
                    <?php
                    } else {
                        $colTitle = $fieldMetadata->name; ?>
                        <th id="title_<?= $colTitle ?>"><?= $colTitle ?></th>
                <?php }
                    $i++;
                endwhile; ?>
            </tr>
            <?php
            $i = 0;
            while ($line = $result->fetch_assoc()) :
                $j = 0; ?>
                <tr>
                    <?php foreach ($line as $cell) :
                        if ($j === 0) { ?>
                            <td class="add-column"><button type="submit" class="btn btn-primary" value="<?= $cell ?>" name="spouseID">Add</button></td>
                        <?php
                        } else { ?>
                            <td id="<?= $i ?>_<?= $j ?>"> <?= $cell ?> </td>
                    <?php }
                        $j++;
                    endforeach; ?>
                </tr>

            <?php
                $i++;
            endwhile;
            ?>
        </table>
    </form>
<?php endif; } ?>