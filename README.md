[![PHPStan ](https://img.shields.io/badge/PHPStan-Level%206%20-2a5ea7.svg)](https://github.com/szepeviktor/phpstan-wordpress)

## Description

A plugin for getting data from Roblox API.
Supports basic gameData, activePlayers, visits, userId, userAvatarThumbnail,
userAvatarThumbnailUrl, assetThumbnail, assetThumbnailUrl, gameIcon, gameIconUrl, groupRoles,
groupData, groupRank, groupMembers, badgeInfo, userInfo, assetDetails.

## Installation & Usage

1. Install the plugin in the /wp-content/plugins/ directory.
2. Activate the plugin via the Plugins menu in the WordPress admin panel.
3. Use special shortcodes in blog content

## Roadmap / Development Plan

- Unified shortcode error format (clear for the user, details in the admin log)
- Validation and normalization of arguments in all shortcodes (types, ranges, allowed values)
- Customizable error behavior: empty / error text / placeholder
- Safe output: text/URL escaping, protection from unsafe HTML
- Improved `json_key`: understandable errors, stable navigation through nested data/arrays, "no key" handling
- Advanced cache settings: default TTL and individual TTL for each data source
- Clear rules for which optional args affect the cache + the ability to override this via filters
- Request storm protection: locking on updating one key
- Stale-while-revalidate: return the old cache while an update is in progress
- Rate limit protection: throttling, retry with backoff at 429/5xx, separate limits for "high-end" users Endpoints (e.g., asset thumbnails)
- Admin settings page: user agent, timeouts, retries, TTL, error mode, placeholders
- Diagnostics admin page: response codes, request time, cache hit/miss counters, recent error log
- Cache management buttons: clear all / by data source / by key
- Enabled debug mode (detailed request logs)
- Universal shortcode "roblox_api" (endpoint + params) for advanced use
- Output formats: text/json/html + simple output templates (template with field inserts)
- Set of WP hooks/filters: result modification, registration of custom data sources, cache settings
- Object cache support (Redis/Memcached) and correct work with transients
- Domain restrictions/checks for Roblox CDN images (whitelist), safe image insertion
- i18n: translatable strings (admin/messages)
- Batch requests where the API allows (less HTTP per page)
- Gutenberg blocks as an alternative to shortcodes for popular scenarios
- Game/user/group card widgets/shortcodes (ready-made UI components)
- New data sources (as needed): follower/following, friend count, presence/online status (if available), additional game/group data
- Pagination where the API returns cursors (e.g., role members) + optional cursor output
- Unit tests: argument parser, `json_key`, URL generation, cache keys
- Integration tests with HTTP mocks (without real Roblox requests)
- CI (GitHub Actions): PHPStan/PHPCS/tests for each PR
- Auto-checking of README examples (to ensure documentation matches behavior)

# Usage

- [Usage](#usage)
    * [Basic Usage](#basic-usage)
    * [Data Sources](#data-sources)
        + [gameData](#gamedata)
        + [activePlayers](#activeplayers)
        + [visits](#visits)
        + [userId](#userid)
        + [userAvatarThumbnail](#useravatarthumbnail)
        + [userAvatarThumbnailUrl](#useravatarthumbnailurl)
        + [assetThumbnail](#assetthumbnail)
        + [assetThumbnailUrl](#assetthumbnailurl)
        + [gameIcon](#gameicon)
        + [gameIconUrl](#gameiconurl)
        + [groupRoles](#grouproles)
        + [groupRank](#grouprank)
        + [groupData](#groupdata)
        + [groupMembers](#groupmembers)
        + [badgeInfo](#badgeinfo)
        + [userInfo](#userinfo)
        + [assetDetails](#assetdetails)
        + [groupRolesList](#grouproleslist)
        + [gameNameDescription](#gamenamedescription)
        + [universeInfo](#universeinfo)
        + [userGames](#usergames)
        + [userPlaceVisits](#userplacevisits)
        + [gameEvents](#gameevents)
        + [groupRoleMembers](#grouprolemembers)
    * [Handling JSON data](#handling-json-data)
        + [JSON keys](#json-keys)
        + [Pretty-printing JSON data](#pretty-printing-json-data)
    * [FAQs](#faqs)
        + [How do I obtain the Universe ID of a game?](#how-do-i-obtain-the-universe-id-of-a-game)
        + [Embedding images from the Roblox CDN](#embedding-images-from-the-roblox-cdn)
        + [Migrating from the old parser functions](#migrating-from-the-old-parser-functions)
    * [Configuration](#configuration)
        + [`Cache`](#cache)
        + [`Optional argument`](#optional-argument)
        + [`User agent`](#user-agent)

## Basic Usage

To use any data source, you can use the shortcode `[roblox_... ...]`. The first part is the name of the
data source with prefix `roblox_`, and the arguments are the arguments for the data source.

For example, to get the ID of a user named `spss1212 `, you can use:

```
[roblox_userId username="spss1212" ]
[roblox_userId username=spss1212 ]
[roblox_userId spss1212 ]
```

This example uses the data source `userId` and provides one required argument, `spss1212 `.

Each data source has a set number of required arguments. Additionally, there are some optional arguments that are
specified in the `key=value` format.

## Data Sources

### gameData

Provides information about a game/place in the [JSON format](#handling-json-data).

#### Example

Get all JSON data of a game:

```
[roblox_gameData 6483209208 132813250731469 ]
```

Get the name of the creator of a game:

```
[roblox_gameData 6483209208 132813250731469 json_key="creator->name" ]
```

#### Required arguments

| Name         | Description                                  | Type       |
|--------------|----------------------------------------------|------------|
| `UniverseId` | The [universe ID](#universe-id) of the game. | Numeric ID |
| `PlaceId`    | The place ID of the game.                    | Numeric ID |

### activePlayers

Provides the number of active players in a place. Requires [gameData](#gameData) to be enabled.

#### Example

Get the number of active players in a place:

```
[roblox_activePlayers 6483209208 132813250731469 ]
```


#### Required Arguments

| Name         | Description                                  | Type       |
|--------------|----------------------------------------------|------------|
| `UniverseId` | The [universe ID](#universe-id) of the game. | Numeric ID |
| `PlaceId`    | The place ID of the game.                    | Numeric ID |

### visits

Provides the number of visits to a place. Requires [gameData](#gameData) to be enabled.

#### Example

Get the number of visits to a place:

```
[roblox_visits 6483209208 132813250731469 ]
```


#### Required Arguments

| Name         | Description                                  | Type       |
|--------------|----------------------------------------------|------------|
| `UniverseId` | The [universe ID](#universe-id) of the game. | Numeric ID |
| `PlaceId`    | The place ID of the game.                    | Numeric ID |

### userId

Provides the user ID for a given username.

#### Example

Get the user ID of a user:

```
[roblox_userId  spss1212  ]
```

#### Required Arguments

| Name       | Description               | Type   |
|------------|---------------------------|--------|
| `Username` | The username of the user. | String |

### userAvatarThumbnail

Provides data about a user's avatar thumbnail in the [JSON format](#handling-json-data).

#### Example

Get the data about the user avatar thumbnail of spss1212  (ID 10335492021):

```
[roblox_userAvatarThumbnail  10335492021  150x150 json_key="targetId" ]
[roblox_userAvatarThumbnail  10335492021  150x150 is_circular=true  ]
```

#### Required Arguments

| Name            | Description                | Type                                                                                                                                                      |
|-----------------|----------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------|
| `UserId`        | The user ID of the user.   | Numeric ID                                                                                                                                                |
| `ThumbnailSize` | The size of the thumbnail. | String (`30x30`, `48x48`, `60x60`, `75x75`, `100x100`, `110x110`, `140x140`, `150x150`, `150x200`, `180x180`, `250x250`, `352x352`, `420x420`, `720x720`) |

#### Optional Arguments

| Name          | Description                               | Type                                              | Default | Example               |
|---------------|-------------------------------------------|---------------------------------------------------|---------|-----------------------|
| `is_circular` | Whether the thumbnail should be circular. | Boolean                                           | `false` | `is_circular=true`    |
| `format`      | The format of the thumbnail.              | String (`Png`, `Webp`)                            | `Png`   | `format=Webp`         |
| `json_key`    | Key of json.                              | String (`targetId`, `state`, `imageUrl`,`version` | ``      | `json_key="targetId"` |

### userAvatarThumbnailUrl

Provides the URL of a user's avatar thumbnail. Allows [embedding](#Embedding-images-from-the-Roblox-CDN) the avatar
image. Requires [userAvatarThumbnail](#userAvatarThumbnail) to be enabled.

#### Example

Get the URL of the user avatar thumbnail of spss1212  (ID 10335492021):

```
[roblox_userAvatarThumbnailUrl  10335492021  150x150 ]
```

#### Required Arguments

| Name            | Description                | Type                                                                                                                                                      |
|-----------------|----------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------|
| `UserId`        | The user ID of the user.   | Numeric ID                                                                                                                                                |
| `ThumbnailSize` | The size of the thumbnail. | String (`30x30`, `48x48`, `60x60`, `75x75`, `100x100`, `110x110`, `140x140`, `150x150`, `150x200`, `180x180`, `250x250`, `352x352`, `420x420`, `720x720`) |

#### Optional Arguments

| Name          | Description                               | Type                   | Default | Example            |
|---------------|-------------------------------------------|------------------------|---------|--------------------|
| `is_circular` | Whether the thumbnail should be circular. | Boolean                | `false` | `is_circular=true` |
| `format`      | The format of the thumbnail.              | String (`Png`, `Webp`) | `Png`   | `format=Webp`      |

### assetThumbnail

> [!WARNING]
> Roblox enforces a stricter rate limit on the API used for this than on the other APIs.
> It is in general recommended to use it at most once per page.

Provides the data about an asset thumbnail in the [JSON format](#handling-json-data).

#### Example

Get the data about the asset thumbnail of the asset with ID 102611803:

```
[roblox_assetThumbnail  1962446128  140x140 json_key="targetId"]
```

#### Required Arguments

| Name            | Description                | Type                                                                                                                                                                                                                                                                 |
|-----------------|----------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `AssetId`       | The asset ID of the asset. | Numeric ID                                                                                                                                                                                                                                                           |
| `ThumbnailSize` | The size of the thumbnail. | String (`30x30`, `42x42`, `50x50`, `60x62`, `75x75`, `110x110`, `140x140`, `150x150`, `160x100`, `160x600`, `250x250`, `256x144`, `300x250`, `304x166`, `384x216`, `396x216`, `420x420`, `480x270`, `512x512`, `576x324`, `700x700`, `728x90`, `768x432`, `1200x80`) |

#### Optional Arguments

| Name          | Description                               | Type                                              | Default | Example               |
|---------------|-------------------------------------------|---------------------------------------------------|---------|-----------------------|
| `is_circular` | Whether the thumbnail should be circular. | Boolean                                           | `false` | `is_circular=true`    |
| `format`      | The format of the thumbnail.              | String (`Png`, `Webp`)                            | `Png`   | `format=Webp`         |
| `json_key`    | Key of json.                              | String (`targetId`, `state`, `imageUrl`,`version` | ``      | `json_key="targetId"` |

### assetThumbnailUrl

> [!WARNING]
> Roblox enforces a stricter rate limit on the API used for this than on the other APIs.
> It is in general recommended to use it at most once per page.

Provides the URL of an asset thumbnail. Allows [embedding](#Embedding-images-from-the-Roblox-CDN) the asset image.
Requires [assetThumbnail](#assetThumbnail) to be enabled.

#### Example

Get the URL of the asset thumbnail of the asset with ID 102611803:

```
[roblox_assetThumbnailUrl  1962446128  140x140 ]
```

#### Required Arguments

| Name            | Description                | Type                                                                                                                                                                                                                                                                 |
|-----------------|----------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `AssetId`       | The asset ID of the asset. | Numeric ID                                                                                                                                                                                                                                                           |
| `ThumbnailSize` | The size of the thumbnail. | String (`30x30`, `42x42`, `50x50`, `60x62`, `75x75`, `110x110`, `140x140`, `150x150`, `160x100`, `160x600`, `250x250`, `256x144`, `300x250`, `304x166`, `384x216`, `396x216`, `420x420`, `480x270`, `512x512`, `576x324`, `700x700`, `728x90`, `768x432`, `1200x80`) |

#### Optional Arguments

| Name          | Description                               | Type                   | Default | Example            |
|---------------|-------------------------------------------|------------------------|---------|--------------------|
| `is_circular` | Whether the thumbnail should be circular. | Boolean                | `false` | `is_circular=true` |
| `format`      | The format of the thumbnail.              | String (`Png`, `Webp`) | `Png`   | `format=Webp`      |

### gameIcon

Provides the data about a game icon in the [JSON format](#handling-json-data).

#### Example

Get the data about the game icon of the game with ID 132813250731469:

```
[roblox_gameIcon 132813250731469 150x150 ]
[roblox_gameIcon 132813250731469 150x150 return_policy=ForcePlaceHolder]
```

#### Required Arguments

| Name            | Description               | Type                                                                    |
|-----------------|---------------------------|-------------------------------------------------------------------------|
| `PlaceId`       | The place ID of the game. | Numeric ID                                                              |
| `ThumbnailSize` | The size of the icon.     | String (`50x50`, `128x128`, `150x150`, `256x256`, `420x420`, `512x512`) |

#### Optional Arguments

| Name            | Description                          | Type                                       | Default       | Example                     |
|-----------------|--------------------------------------|--------------------------------------------|---------------|-----------------------------|
| `is_circular`   | Whether the icon should be circular. | Boolean                                    | `false`       | `is_circular=true`          |
| `format`        | The format of the icon.              | String (`Png`, `Webp`)                     | `Png`         | `format=Webp`               |
| `return_policy` | The return policy of the icon.       | String (`PlaceHolder`, `ForcePlaceHolder`) | `PlaceHolder` | `return_policy=PlaceHolder` |

### gameIconUrl

Provides the URL of a game icon. Allows [embedding](#Embedding-images-from-the-Roblox-CDN) the game icon image.
Requires [gameIcon](#gameIcon) to be enabled.

#### Example

Get the URL of the game icon of the game with ID 132813250731469:

```
[roblox_gameIconUrl 132813250731469 150x150 ]
```

#### Required Arguments

| Name            | Description               | Type                                                                    |
|-----------------|---------------------------|-------------------------------------------------------------------------|
| `PlaceId`       | The place ID of the game. | Numeric ID                                                              |
| `ThumbnailSize` | The size of the icon.     | String (`50x50`, `128x128`, `150x150`, `256x256`, `420x420`, `512x512`) |

#### Optional Arguments

| Name            | Description                          | Type                                       | Default       | Example                     |
|-----------------|--------------------------------------|--------------------------------------------|---------------|-----------------------------|
| `is_circular`   | Whether the icon should be circular. | Boolean                                    | `false`       | `is_circular=true`          |
| `format`        | The format of the icon.              | String (`Png`, `Webp`)                     | `Png`         | `format=Webp`               |
| `return_policy` | The return policy of the icon.       | String (`PlaceHolder`, `ForcePlaceHolder`) | `PlaceHolder` | `return_policy=PlaceHolder` |

### groupRoles

Provides all group roles a user has in all groups they have joined in the [JSON format](#handling-json-data).

[Official API documentation](https://groups.roblox.com//docs/index.html?urls.primaryName=Groups%20Api%20v1#operations-Membership-get_v1_users__userId__groups_roles)

#### Example

Get all JSON data of the group roles of a user:

```
[roblox_groupRoles  418245610335492021 ]
```

#### Required Arguments

| Name     | Description              | Type       |
|----------|--------------------------|------------|
| `UserId` | The user ID of the user. | Numeric ID |

### groupRank

Provides the name of a user's rank in a group.
Requires [groupRoles](#groupRoles) to be enabled.

#### Example

Get the name of the rank of the user with ID `418245610335492021` in the group with ID `3620943`:

```
[roblox_groupRank  3620943  418245610335492021 ]
```

#### Required Arguments

| Name      | Description                | Type       |
|-----------|----------------------------|------------|
| `GroupId` | The group ID of the group. | Numeric ID |
| `UserId`  | The user ID of the user.   | Numeric ID |

### groupData

Provides data about a group in the [JSON format](#handling-json-data).

[Official API documentation](https://groups.roblox.com//docs/index.html?urls.primaryName=Groups%20Api%20v1#operations-Groups-get_v1_groups__groupId_)

#### Example

Get all JSON data of a group:

```
[roblox_groupData  3620943 json_key="owner" ]
```

#### Required Arguments

| Name      | Description                | Type       |
|-----------|----------------------------|------------|
| `GroupId` | The group ID of the group. | Numeric ID |

### groupMembers

Provides the number of members in a group.
Requires [groupData](#groupData) to be enabled.

#### Example

Get the number of members in a group:

```
[roblox_groupMembers  3620943 ]
```



#### Required Arguments

| Name      | Description                | Type       |
|-----------|----------------------------|------------|
| `GroupId` | The group ID of the group. | Numeric ID |

### badgeInfo

Provides information about a badge in the [JSON format](#handling-json-data).

#### Example

Get all JSON data of a badge:

```
[roblox_badgeInfo  4488119458388820]
```

#### Required Arguments

| Name      | Description                | Type       |
|-----------|----------------------------|------------|
| `BadgeId` | The badge ID of the badge. | Numeric ID |

### userInfo

Provides information about a user in the [JSON format](#handling-json-data).

#### Example

Get all JSON data of a user:

```
[roblox_userInfo  10335492021 ]
```

#### Required Arguments

| Name     | Description              | Type       |
|----------|--------------------------|------------|
| `UserId` | The user ID of the user. | Numeric ID |

### assetDetails

Provides information about an asset in the [JSON format](#handling-json-data).

#### Example

Get all JSON data of an asset:

```
[roblox_assetDetails  102611803 json_key="ProductType"]
```

#### Required Arguments

| Name      | Description                | Type       |
|-----------|----------------------------|------------|
| `AssetId` | The asset ID of the asset. | Numeric ID |

### groupRolesList

Provides a list of roles in a group in the [JSON format](#handling-json-data).

#### Example

Get the roles of a group:

```
    [roblox_groupRolesList  5353743 ]
```

#### Required Arguments

| Name      | Description                | Type       |
|-----------|----------------------------|------------|
| `GroupId` | The group ID of the group. | Numeric ID |

### gameNameDescription

Provides the name and description of a game in all supported languages in the [JSON format](#handling-json-data).

#### Example

Get the name and description of a game:

```
[roblox_gameNameDescription  6483209208 ]
```

Get the description of a game in English:

```
\\TODO
[roblox_gameNameDescription  6483209208 | json_key=data->0->description ]
```

#### Required Arguments

| Name         | Description                                  | Type       |
|--------------|----------------------------------------------|------------|
| `UniverseId` | The [universe ID](#universe-id) of the game. | Numeric ID |

### universeInfo

Provides info about a universe in the [JSON format](#handling-json-data).

#### Example

Get info about a universe:

```
[roblox_universeInfo 4864117649 ]
```

Get the privacy type of a universe:

```
[roblox_universeInfo  4864117649  json_key=privacyType ]
```

#### Required Arguments

| Name         | Description                                  | Type       |
|--------------|----------------------------------------------|------------|
| `UniverseId` | The [universe ID](#universe-id) of the game. | Numeric ID |

### userGames

Provides a list of games a user has created in the [JSON format](#handling-json-data).

Note that it is not possible to get more than 50 games.

#### Example

Get the list of games a user has created:

```
[roblox_userGames 1995870730 ]
```

#### Required Arguments

| Name     | Description              | Type       |
|----------|--------------------------|------------|
| `UserId` | The user ID of the user. | Numeric ID |

#### Optional Arguments

| Name         | Description                            | Type                      | Default |
|--------------|----------------------------------------|---------------------------|---------|
| `limit`      | The maximum number of games to return. | Numeric ID (10, 25 or 50) | `50`    |
| `sort_order` | The order to sort the games.           | String (`Asc`, `Desc`)    | `Asc`   |

### userPlaceVisits

Provides the number of visits of all places a user has created.

Note that due to performance reasons, only the views of the first 50 places of the user are returned.

#### Example

Get the number of visits of all places a user has created:

```
[roblox_userPlaceVisits  1995870730 ]
```

#### Required Arguments

| Name     | Description              | Type       |
|----------|--------------------------|------------|
| `UserId` | The user ID of the user. | Numeric ID |

#### Optional Arguments

| Name         | Description                                                                                                                       | Type                      | Default |
|--------------|-----------------------------------------------------------------------------------------------------------------------------------|---------------------------|---------|
| `limit`      | The maximum number of games to consider in the calculation.                                                                       | Numeric ID (10, 25 or 50) | `50`    |
| `sort_order` | The order to sort the games. This is used by the api and may change the results if the user has more games than the limit allows. | String (`Asc`, `Desc`)    | `Asc`   |

### gameEvents

Provides a list of events happening in a universe.

#### Example

Get the events in a universe:

```
[roblox_gameEvents  6597877862 ]
```

Get the title of the first event in a universe:

```
//TODO
[roblox_gameEvents  6597877862  json_key=0->title ]
```

#### Required Arguments

| Name         | Description                                  | Type       |
|--------------|----------------------------------------------|------------|
| `UniverseId` | The [universe ID](#universe-id) of the game. | Numeric ID |

### groupRoleMembers

Provides a list of users who have a certain role in a group.

#### Example

List of product developers in the SRC group:

```
[roblox_groupRoleMembers  3620943  31072726  limit=100 ]
```

#### Required Arguments

| Name      | Description                | Type       |
|-----------|----------------------------|------------|
| `GroupId` | The group ID of the group. | Numeric ID |
| `RoleId`  | The role ID of the role.   | Numeric ID |

#### Optional Arguments

| Name         | Description                            | Type                         | Default |
|--------------|----------------------------------------|------------------------------|---------|
| `limit`      | The maximum number of users to return. | Numeric ID (10, 25, 50, 100) | `50`    |
| `sort_order` | The order to sort the users.           | String (`Asc`, `Desc`)       | `Asc`   |


## Handling JSON data

### JSON keys

Some data sources return plain JSON data from the Roblox API. To parse this data, you can either use Lua (with the
Scribunto extension) or use the `json_key` optional argument:

```
[roblox_userInfo  10335492021  json_key=created ]
```

This example gets the user info of the user with the ID `10335492021` and returns the `created` key from the JSON data.

Nested keys can be accessed by separating them with '->', e.g.:

```
//TODO
[roblox_gameData  6483209208  132813250731469  json_key=creator->name ]
```

To access an item in an array, you can use the index of the item, e.g.:

```
//TODO
[roblox_gameData | 6483209208 | 132813250731469 | json_key=allowedGearGenres->0 ]
```

### Pretty-printing JSON data

To pretty-print JSON data, you can use the `pretty` optional argument:

```
[roblox_userInfo | 10335492021 | pretty=true ]
```

## FAQs

<a id="universe-id"></a>

### How do I obtain the Universe ID of a game?

To get the universe ID of a place, input the place ID to this API:

```
https://apis.roblox.com/universes/v1/places/<GAMEID>/universe
```

### Embedding images from the Roblox CDN

The result of the `[roblox_UserAvatarThumbnailUrl]` parser function can be used to embed avatar images.

## Configuration

### Cache

Default caching expiries:

| Data source           | Expiry            |
|-----------------------|-------------------|
| `*` (default)         | 86400 (24 hours)  |

> [!WARNING]
> Lower cache expiry times can lead to more requests to the Roblox API, which can lead to rate limiting and decreased
> site performance.


### Optional argument

An array of optional arguments that should affect caching.

Some optional arguments, such as `pretty`, do not affect the API result.
Some do, such as `format`, but are not included in the default value since it does not matter a lot which image format
is served.

### User Agent

The user agent that should be used when making requests to the Roblox API. By default, it uses the
default one random
