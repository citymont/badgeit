Badge It!
=========

Transform users interactions into game !

```
$badge = new Badgeit();
$badge->prfxr = "fanbase_"; mysql table prefix
$badge->tbl_fan = "fan"; mysql user table
```

Create an exploit
```
$params = array(EXPLOIT_NAME,POINTS,CATEGORY);
$badge->createExploit($params);
```

User make an exploit
```
$badge->savePlayerExploit(EXPLOIT_ID,USER_ID,array('params1'=>'http://goog1111le.fr'),DATE);
```

Update user stats
```
$badge->updatePlayerPoints(USER_ID);
```

Show ranking
```
$Classement = $badge->showPlayerClassement();
```

Create a badge
```
$params = array(BADGE_NAME,USER_ADVANTAGE,RULES,0,1,'');
$badge->createBadge($params);
```
RULES exemple : id_exploit:10>10;id_exploit:4>5
*( at least 10 EXPLOIT_ID=10 and 5 EXPLOIT_ID=4 )*

Check if the user earn badges
```
$badge->updatePlayerBadge(USER_ID);
```
