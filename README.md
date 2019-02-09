# Deploy automation via webhooks from Github


## Add webhook

Open 'Webhooks' in repo settings and add new webhook, where Payload URL is `example.com/listener.php`. Choose 'Content type
': `application/json`

## Add 2 subdomains

Where alpha.example.com to alpha-testing, and beta.example.com for beta-testing.

## Create 2 additional branches

Where alpha branch for alpha.example.com, and beta branch for beta.example.com.

## Check path to subdomains directory and branch names

Example deploy file (running in listener.php):
```$xslt
cd example.com                  // cd to dir with website
git fetch origin master         // fetching
git reset --hard origin/master  // reset to last commit
chown -R www-data:www-data .    // your web-app user from nginx or apache2 settings
git checkout master             // switch to last version on branch
```

## Deploy

After push action with message 'Merge pull...' in tracking branch deploy start automatically.

## Enjoy!