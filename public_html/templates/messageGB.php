            <ul>
                <li>
                    <span>
                        <a href="/?sort=name&order=<?=($GB["message"]["sort"]=="name"&&$GB["message"]["order"]=="asc")?"desc":"asc"?>">Имя</a>
                        <?=($GB["message"]["sort"]=="name")?($GB["message"]["order"]=="asc"?"&nbsp;&uarr;":"&nbsp;&darr;"):""?>
                    </span>
                    <span>
                        <a href="/?sort=email&order=<?=($GB["message"]["sort"]=="email"&&$GB["message"]["order"]=="asc")?"desc":"asc"?>">Email</a>
                        <?=($GB["message"]["sort"]=="email")?($GB["message"]["order"]=="asc"?"&nbsp;&uarr;":"&nbsp;&darr;"):""?>
                    </span>
                    <span>
                        <a href="/?sort=datetime&order=<?=($GB["message"]["sort"]=="datetime"&&$GB["message"]["order"]=="asc")?"desc":"asc"?>">Дата и время</a>
                        <?=($GB["message"]["sort"]=="datetime")?($GB["message"]["order"]=="asc"?"&nbsp;&uarr;":"&nbsp;&darr;"):""?>
                    </span>
                    <span>Сообщение</span>
                </li>
<?foreach($GB["message"]["row"] as $row){?>
                <li>
                    <span><?=htmlspecialchars($row["name"])?></span>
                    <span><a href="mailto:<?=htmlspecialchars($row["email"])?>"><?=htmlspecialchars($row["email"])?></a></span>
                    <span><?=htmlspecialchars(date("d.m.Y H:i:s", $row["datetime"]))?></span>
                    <span><?=htmlspecialchars($row["message"])?></span>
                </li>
<?}?>
            </ul>
<?if($GB["message"]["total"]>1 ){?>
            <nav>
                <ul>
                    <li><span>Страницы</span></li>
                    <?for($i=1;$i<=$GB["message"]["total"]; $i++){?>
                    <li>
                        <?if($i==$GB["message"]["current"]){?>
                        <span><?=$i?></span>
                        <?}else{?>
                        <?if($i==1){?>
                            <a href="/?sort=<?=$GB["message"]["sort"]?>&order=<?=$GB["message"]["order"]?>"><?=$i?></a>
                        <?}else{?>
                            <a href="/<?=$i?>.html?sort=<?=$GB["message"]["sort"]?>&order=<?=$GB["message"]["order"]?>"><?=$i?></a>
                        <?}?>
                        <?}?>
                    </li>
                    <?}?>
                </ul>
            </nav>
<?}?>
