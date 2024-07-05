            <ul>
                <li>
                    <span>Операция</span>
                    <span>IP</span>
                    <span>Браузер</span>
                    <span>Имя</span>
                    <span>Email</span>
                    <span>Дата и время</span>
                    <span>Сообщение</span>
                </li>
<?foreach($ADMIN as $row){?>
                <li>
                    <span><button data-message-id="<?=$row["id"]?>">Удалить</button></span>
                    <span><?=htmlspecialchars($row["ip"])?>&nbsp;</span>
                    <span><?=htmlspecialchars($row["browser"])?> <?=htmlspecialchars($row["version"])?>&nbsp;</span>
                    <span><?=htmlspecialchars($row["name"])?></span>
                    <span><a href="mailto:<?=htmlspecialchars($row["email"])?>"><?=htmlspecialchars($row["email"])?></a></span>
                    <span><?=htmlspecialchars(date("d.m.Y H:i:s", $row["datetime"]))?></span>
                    <span><?=htmlspecialchars($row["message"])?></span>
                </li>
<?}?>
            </ul>

