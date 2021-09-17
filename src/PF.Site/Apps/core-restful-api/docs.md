## GET /blog

_Browse blogs_

### Example URI
> http://example.com/restful_api/blog?search[search]=Bermuda&limit=10&page=1

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| search[search] | string | no | Keyword for searching |
| user_id | number | no | Browse by owner id |
| sort | string | no | Support sort return results |
| limit | number | no | Limit return results |
| page | number | no | Paging return results |
| item_id | number | no | Support search blogs on item (pages/groups) |
| module_id | string | no | Support search blogs on item (pages/groups) |
| category | number | no | Support search blogs by category id |
| tag | string | no | Support search blogs by tags |
| view | string | no | Support some view mode: __spam__, __pending__, __my__, __draft__ |

### Response
```
{
  "status": "success",
  "data": [
    {
      "text": "The Bermuda Triangle, also known as the Devil's Triangle, is a loosely-defined region in the western part of the North Atlantic Ocean, where a number of aircraftand ships are said to have disappeared under mysterious circumstances. Most reputable sources dismiss the idea that there is any mystery. The vicinity of the Bermuda Triangle is one of the most heavily traveled shipping lanes in the world, with ships frequently crossing through it for ports in the Americas, Europe, and the Caribbean islands. Cruise ships and pleasure craft regularly sail through the region, and commercial and private aircraft routinely fly over it.\r\n\r\nPopular culture has attributed various disappearances to the paranormal or activity by extraterrestrial beings. Documented evidence indicates that a significant percentage of the incidents were spurious, inaccurately reported, or embellished by later authors.",
      "user_id": "1",
      "blog_id": "5",
      "title": "Bermuda Triangle",
      "time_stamp": "1481594689",
      "time_update": "1481594990",
      "is_approved": "1",
      "privacy": "0",
      "post_status": "1",
      "total_comment": "0",
      "total_attachment": "0",
      "total_view": "0",
      "total_like": "0",
      "module_id": "blog",
      "item_id": "0",
      "categories": [],
      "tag_list": ""
    }
  ],
  "messages": []
}
```

## POST /blog

_Post new blog_

### Example URI
> http://example.com/restful_api/blog

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| val[title] | string | yes | Blog title|
| val[text] | string | yes | Blog text |
| val[categories] | array | no | Blog categories |
| val[privacy] | number | no | Limit return results |
| val[tag_list] | string | no | Tags list for the blog |
| val[publish] | boolean | no | Publish blog |
| val[draft] | boolean | no | Save blog as draft |
| val[module_id] | string | no | Support post blog on item (pages/groups...) |
| val[item_id] | string | no | Support post blog on item (pages/groups...) |

### Response
```
{
  "status": "success",
  "data": {
    "blog_id": "7",
    "user_id": "1",
    "title": "Santa Claus",
    "time_stamp": "1481596113",
    "time_update": "0",
    "is_approved": "1",
    "privacy": "0",
    "post_status": "1",
    "total_comment": "0",
    "total_attachment": "0",
    "total_view": "0",
    "total_like": "0",
    "module_id": "blog",
    "item_id": "0",
    "text": "Santa Claus, also known as Saint Nicholas, Saint Nick, Kris Kringle, Father Christmas, or simply Santa (Santy in Hiberno-English), is a legendary figure of Western culture who is said to bring gifts to the homes of well-behaved (&quot;good&quot; or &quot;nice&quot;) children on Christmas Eve (24 December) and the early morning hours of Christmas Day (25 December). The modern Santa Claus grew out of traditions surrounding the historical Saint Nicholas, a fourth-century Greek bishop and gift-giver of Myra, the British figure of Father Christmas, the Dutch figure of Sinterklaas (himself based on Saint Nicholas), the German figure of the Christkind (a fabulized Christ Child), and the holidays of Twelfth Night and Epiphany and their associated figures of the Three Kings (based on the gift-giving Magi of the Nativity) and Befana. Some maintain Santa Claus also absorbed elements of the Germanic god Wodan, who was associated with the pagan midwinter event of Yule and led the Wild Hunt, a ghostly procession through the sky.",
    "categories": [
      {
        "blog_id": "7",
        "category_id": "1",
        "category_name": "Business",
        "user_id": "0"
      }
    ],
    "tag_list": ""
  },
  "messages": [
    "Blog successfully added."
  ]
}
```

## GET /blog/:id

_Get information of a specific blog_

### Example URI
> http://example.com/restful_api/blog/7

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Blog id|

### Response
```
{
  "status": "success",
  "data": {
    "blog_id": "7",
    "user_id": "1",
    "title": "Santa Claus",
    "time_stamp": "1481596113",
    "time_update": "0",
    "is_approved": "1",
    "privacy": "0",
    "post_status": "1",
    "total_comment": "0",
    "total_attachment": "0",
    "total_view": "0",
    "total_like": "0",
    "module_id": "blog",
    "item_id": "0",
    "text": "Santa Claus, also known as Saint Nicholas, Saint Nick, Kris Kringle, Father Christmas, or simply Santa (Santy in Hiberno-English), is a legendary figure of Western culture who is said to bring gifts to the homes of well-behaved (&quot;good&quot; or &quot;nice&quot;) children on Christmas Eve (24 December) and the early morning hours of Christmas Day (25 December). The modern Santa Claus grew out of traditions surrounding the historical Saint Nicholas, a fourth-century Greek bishop and gift-giver of Myra, the British figure of Father Christmas, the Dutch figure of Sinterklaas (himself based on Saint Nicholas), the German figure of the Christkind (a fabulized Christ Child), and the holidays of Twelfth Night and Epiphany and their associated figures of the Three Kings (based on the gift-giving Magi of the Nativity) and Befana. Some maintain Santa Claus also absorbed elements of the Germanic god Wodan, who was associated with the pagan midwinter event of Yule and led the Wild Hunt, a ghostly procession through the sky.",
    "categories": [
      {
        "blog_id": "7",
        "category_id": "1",
        "category_name": "Business",
        "user_id": "0"
      }
    ],
    "tag_list": ""
  },
  "messages": []
}
```

## PUT /blog/:id

_Update information for a specific blog_

### Example URI
> http://example.com/restful_api/blog/7

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Blog id|
| val[title] | string | no | Blog title|
| val[text] | string | no | Blog text |
| val[categories] | array | no | Blog categories |
| val[privacy] | number | no | Limit return results |
| val[tag_list] | string | no | Tags list for the blog |
| val[publish] | boolean | no | Publish blog |

### Response
```
{
  "status": "success",
  "data": {
    "blog_id": "7",
    "user_id": "1",
    "title": "Santa Claus",
    "time_stamp": "1481596113",
    "time_update": "1481597249",
    "is_approved": "1",
    "privacy": "0",
    "post_status": "1",
    "total_comment": "0",
    "total_attachment": "0",
    "total_view": "0",
    "total_like": "0",
    "module_id": "blog",
    "item_id": "0",
    "text": "Santa Claus, also known as Saint Nicholas, Saint Nick, Kris Kringle, Father Christmas, or simply Santa (Santy in Hiberno-English), is a legendary figure of Western culture who is said to bring gifts to the homes of well-behaved (&quot;good&quot; or &quot;nice&quot;) children on Christmas Eve (24 December) and the early morning hours of Christmas Day (25 December). The modern Santa Claus grew out of traditions surrounding the historical Saint Nicholas, a fourth-century Greek bishop and gift-giver of Myra, the British figure of Father Christmas, the Dutch figure of Sinterklaas (himself based on Saint Nicholas), the German figure of the Christkind (a fabulized Christ Child), and the holidays of Twelfth Night and Epiphany and their associated figures of the Three Kings (based on the gift-giving Magi of the Nativity) and Befana. Some maintain Santa Claus also absorbed elements of the Germanic god Wodan, who was associated with the pagan midwinter event of Yule and led the Wild Hunt, a ghostly procession through the sky.",
    "categories": [
      {
        "blog_id": "7",
        "category_id": "4",
        "category_name": "Family & Home",
        "user_id": "0"
      }
    ],
    "tag_list": ""
  },
  "messages": [
    "Blog successfully updated."
  ]
}
```

## DELETE /blog/:id

_Delete a specific blog_

### Example URI
> http://example.com/restful_api/blog/7

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Blog id|

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "Blog successfully deleted."
  ]
}
```

## GET /event

_Browse events_

### Example URI
> http://example.com/restful_api/event?search[search]=christmas&when=this-month

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| search[search] | string | no | Keyword for searching |
| user_id | string | no | Browse by owner id |
| sort | string | no | Support sort return results, available options: __latest__, __most-liked__, __most-talked__ |
| when | string | no | Browse events by start time, available options: __all-time__, __this-month__, __this-week__, __today__, __upcoming__, __ongoing__ |
| limit | number | no | Limit return results |
| page | number | no | Paging return results |
| item_id | number | no | Support browse events on item (pages/groups) |
| module_id | string | no | Support search events on item (pages/groups) |
| category | number | no | Support search events by category id |
| view | string | no | Support some view mode: __featured__, __pending__, __my__, __attending__, __may-attend__, __not-attending__, __invites__ |

### Response
```
{
  "status": "success",
  "data": [
    {
      "rsvp_id": "1",
      "event_id": "1",
      "view_id": "0",
      "is_featured": "0",
      "is_sponsor": "0",
      "privacy": "0",
      "privacy_comment": "0",
      "module_id": "event",
      "item_id": "0",
      "user_id": "1",
      "title": "Christmas Party",
      "location": "New York",
      "country_iso": "US",
      "country_child_id": "6",
      "postal_code": null,
      "city": null,
      "time_stamp": "1481856813",
      "start_time": "1482598800",
      "end_time": "1482618600",
      "image_path": null,
      "server_id": "0",
      "total_comment": "0",
      "total_like": "0",
      "gmap": null,
      "address": null,
      "description": "Celebrate Christmas 2016 and welcome New Year 2017!!!",
      "event_date": "Saturday, December 24, 2016 6:00 pm - 11:30 pm",
      "categories": [
        [
          "Party",
          "http://example.com/event/category/2/party/"
        ]
      ]
    }
  ],
  "messages": []
}
```

## POST /event

_Post new event_

### Example URI
> http://example.com/restful_api/event

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| val[category] | id | no | Event category id |
| val[title] | string | yes | Event title |
| val[description] | string | no | Event description |
| val[start_hour] | number | yes | Start hour |
| val[start_minute] | number | yes | Start minute |
| val[start_month] | number | yes | Start month |
| val[start_year] | number | yes | Start year |
| val[start_day] | number | yes | Start day |
| val[end_hour] | number | yes | End hour |
| val[end_minute] | number | yes | End minute|
| val[end_day] | number | yes | End day |
| val[end_month] | number | yes | End month |
| val[end_year] | number | yes | End year |
| val[location] | string | yes | Event location |
| val[address] | string | no | Address |
| val[city] | string | no | City |
| val[postal_code] | string | no | Postal Code |
| val[country_iso] | string | no | Country ISO |
| val[country_child_id] | number | no | State id/ Province id |
| val[privacy] | number | yes | Event privacy |
| val[privacy_comment] | number | yes | Share provicy |
| val[module_id] | string | no | parent module id, support post event on item (groups, pages...) |
| val[item_id] | number | no | parent item id, support post event on item (groups, pages...) |

### Response
```
{
  "status": "success",
  "data": {
    "rsvp_id": "1",
    "event_id": "3",
    "view_id": "0",
    "is_featured": "0",
    "is_sponsor": "0",
    "privacy": "1",
    "privacy_comment": "1",
    "module_id": "pages",
    "item_id": "2",
    "user_id": "1",
    "title": "December Offline (Year End party)",
    "location": "Lotus Restaurent",
    "country_iso": "US",
    "country_child_id": "0",
    "postal_code": null,
    "city": null,
    "time_stamp": "1481858333",
    "start_time": "1483075800",
    "end_time": "1483137000",
    "image_path": null,
    "server_id": "0",
    "total_comment": "0",
    "total_like": "0",
    "gmap": null,
    "address": null,
    "description": "Year End party",
    "event_date": "Friday, December 30, 2016 6:30 am - 11:30 pm",
    "categories": [
      [
        "Sports",
        "http://example.com/event/category/4/sports/"
      ]
    ]
  },
  "messages": [
    "Event successfully added."
  ]
}
```

## GET /event/:id

_Get information of a specific event_

### Example URI
> http://example.com/restful_api/event/1

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Event id|

### Response
```
{
  "status": "success",
  "data": {
    "rsvp_id": "1",
    "event_id": "1",
    "view_id": "0",
    "is_featured": "0",
    "is_sponsor": "0",
    "privacy": "0",
    "privacy_comment": "0",
    "module_id": "event",
    "item_id": "0",
    "user_id": "1",
    "title": "Christmas Party",
    "location": "New York",
    "country_iso": "US",
    "country_child_id": "6",
    "postal_code": null,
    "city": null,
    "time_stamp": "1481856813",
    "start_time": "1482598800",
    "end_time": "1482618600",
    "image_path": null,
    "server_id": "0",
    "total_comment": "0",
    "total_like": "0",
    "gmap": null,
    "address": null,
    "description": "Celebrate Christmas 2016 and welcome New Year 2017!!!",
    "event_date": "Saturday, December 24, 2016 6:00 pm - 11:30 pm",
    "categories": [
      [
        "Party",
        "http://example.com/event/category/2/party/"
      ]
    ]
  },
  "messages": []
}
```

## PUT /event/:id

_Update information for a specific event_

### Example URI
> http://example.com/restful_api/event/1

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Event id|
| val[category] | number | no | Event category id |
| val[title] | string | no | Event title |
| val[description] | string | no | Event description |
| val[start_hour] | number | no | Start hour |
| val[start_minute] | number | no | Start minute |
| val[start_month] | number | no | Start month |
| val[start_year] | number | no | Start year |
| val[start_day] | number | no | Start day |
| val[end_hour] | number | no | End hour |
| val[end_minute] | number | no | End minute|
| val[end_day] | number | no | End day |
| val[end_month] | number | no | End month |
| val[end_year] | number | no | End year |
| val[location] | string | bo | Event location |
| val[address] | string | no | Address |
| val[city] | string | no | City |
| val[postal_code] | string | no | Postal Code |
| val[country_iso] | string | no | Country ISO |
| val[country_child_id] | number | no | State id/ Province id |
| val[privacy] | number | no | Event privacy |
| val[privacy_comment] | number | no | Share provicy |
| val[invite][] | array | no | Friend id list to invite |
| val[personal_message] | number | no | Invite message |
| val[emails] | string | no | Email list to invite |
| val[delete_image] | boolean | no | Remove event photo |
| image | file | no | Upload event photo |

### Response
```
{
  "status": "success",
  "data": {
    "rsvp_id": "1",
    "event_id": "1",
    "view_id": "0",
    "is_featured": "0",
    "is_sponsor": "0",
    "privacy": "0",
    "privacy_comment": "0",
    "module_id": "event",
    "item_id": "0",
    "user_id": "1",
    "title": "Christmas Party",
    "location": "New York",
    "country_iso": "US",
    "country_child_id": "6",
    "postal_code": null,
    "city": null,
    "time_stamp": "1481856813",
    "start_time": "1482598800",
    "end_time": "1482618600",
    "image_path": null,
    "server_id": "0",
    "total_comment": "0",
    "total_like": "0",
    "gmap": null,
    "address": null,
    "description": "Celebrate Christmas 2016 and welcome New Year 2017!!!",
    "event_date": "Saturday, December 24, 2016 6:00 pm - 11:30 pm",
    "categories": null
  },
  "messages": [
    "Event successfully updated."
  ]
}
```

## DELETE /event/:id

_Delete a specific event_

### Example URI
> http://example.com/restful_api/event/1

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Event id|

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "Event successfully deleted."
  ]
}
```

## PUT /event/:id/rsvp

_Update RSVP on a specific event_

### Example URI
> http://example.com/restful_api/event/1/rsvp

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Event id|
| rsvp | number | yes | RSVP id, available values: __1__(attending), __2__(may be attending), __3__(not attending) |

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "RSVP successfully updated."
  ]
}
```

## GET /event/:id/guests

_Get guests list of a specific event_

### Example URI
> http://example.com/restful_api/event/1/guests

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Event id |
| rsvp | number | no | RSVP id (default is 1 - attending) |
| limit | number | no | Limit return results |
| page | number | no | Paging return results |

### Response
```
{
  "status": "success",
  "data": [
    {
      "invite_id": "1",
      "event_id": "1",
      "rsvp_id": "1",
      "user_id": "1",
      "time_stamp": "1481859987",
      "user_name": "admin",
      "full_name": "Admin"
    }
  ],
  "messages": []
}
```

## GET /friend

_Browse friends of a specific user_

### Example URI
> http://example.com/restful_api/friend

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| search[search] | string | no | Keyword for searching friends |
| user_id | string | no | Iser id |
| limit | number | no | Limit return results |
| view | string | no | Support some view mode: __mutual__, __online__ |

### Response
```
{
  "status": "success",
  "data": [
    {
      "user_id": "13",
      "friend_id": "26",
      "friend_user_id": "13",
      "is_top_friend": "0",
      "user_name": "profile-13",
      "full_name": "Isabella Jolie"
    },
    {
      "user_id": "7",
      "friend_id": "12",
      "friend_user_id": "7",
      "is_top_friend": "0",
      "user_name": "profile-7",
      "full_name": "Vivian"
    },
    {
      "user_id": "14",
      "friend_id": "28",
      "friend_user_id": "14",
      "is_top_friend": "0",
      "user_name": "profile-14",
      "full_name": "William"
    }
  ],
  "messages": []
}
```

## DELETE /friend

_Remove a friend_

### Example URI
> http://example.com/restful_api/friend?friend_user_id=14

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| friend_user_id | number | yes | Friend id|

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "Friend successfully deleted."
  ]
}
```

## POST /friend/request

_Post new friend request_

### Example URI
> http://example.com/restful_api/friend/request

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| user_id | number | yes | User id that want to be friend with |

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "Friend request successfully sent."
  ]
}
```

## DELETE /friend/request

_Cancel a friend request_

### Example URI
> http://example.com/restful_api/friend

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| user_id | number | yes | Friend id|

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "Friend request successfully deleted."
  ]
}
```

## PUT /friend/request

_Accept or deny a friend request_

### Example URI
> http://example.com/restful_api/friend/request

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| user_id | number | yes | Friend id|
| action | string | yes | Available values: __accept__, __deny__ |

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "Friend request successfully accepted."
  ]
}
```

## GET /search

_Global search_

### Example URI
> http://example.com/restful_api/search?keyword=bermuda&view=blog

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| keyword | string | yes | Keyword for searching |
| limit | number | no | Limit return results |
| page | number | no | Paging return results |
| view | string | no | Search by item type |

### Response
```
{
  "status": "success",
  "data": [
    {
      "item_id": "5",
      "item_title": "Bermuda Triangle",
      "item_time_stamp": "1481594689",
      "item_user_id": "1",
      "item_type_id": "blog",
      "item_photo": "",
      "item_photo_server": "0",
      "item_link": "http://example.com/blog/5/bermuda-triangle/",
      "item_name": "Blog"
    }
  ],
  "messages": []
}
```

## POST /link

_Attach a link_

### Example URI
> http://example.com/restful_api/link

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| val[url] | string | yes | Link to attach |
| val[text] | string | no | Text of feed |
| val[module] | string | no | Support attach link to module item feed (pages/groups) |
| val[item_id] | numbwe | no | Support attach link to module item feed (pages/groups) |
| custom_pages_post_as_page | number | no | Support attach link as page |
| val[user_id] | number | id | Support attach link on user profile |

### Response
```
{
  "status": "success",
  "data": {
    "feed_id": "15",
    "privacy": "0",
    "privacy_comment": "0",
    "type_id": "link",
    "user_id": "1",
    "parent_user_id": "1",
    "item_id": "17",
    "time_stamp": "1481887454",
    "parent_feed_id": "0",
    "parent_module_id": null,
    "time_update": "1481887454",
    "content": null,
    "total_view": "0",
    "can_post_comment": true,
    "feed_title": "[OFFICIAL VIDEO] Hallelujah - Pentatonix - YouTube",
    "feed_status": "Great song for Christmas",
    "feed_link": "http://example.com/admin/?link-id=17",
    "feed_is_liked": false,
    "feed_icon": "<img src=\"http://example.com/PF.Base/theme/frontend/default/style/default/image/feed/link.png\">",
    "enable_like": true,
    "likes": [],
    "feed_like_phrase": ""
  },
  "messages": [
    "Link successfully attached."
  ]
}
```

## GET /photo

_Browse photos_

### Example URI
> http://example.com/restful_api/photo?user_id=10

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| search[search] | string | no | Keyword for searching |
| user_id | number | no | Browse by owner id |
| sort | string | no | Support sort return results, available values: __latest__, __most-liked__, __most-talked__ |
| limit | number | no | Limit return results |
| page | number | no | Paging return results |
| item_id | number | no | Support browse photos on item (pages/groups) |
| module_id | string | no | Support browse photos on item (pages/groups) |
| category | number | no | Support search photos by category id |
| tag | string | no | Support search photos by tags |
| view | string | no | Support some view mode: __pending__, __my__, __featured__ |

### Response
```
{
  "status": "success",
  "data": [
    {
      "is_liked": null,
      "user_id": "1",
      "photo_id": "11",
      "album_id": "0",
      "module_id": null,
      "group_id": "0",
      "privacy": "0",
      "privacy_comment": "0",
      "title": "photo_1",
      "time_stamp": "1481515372",
      "is_featured": "0",
      "is_sponsor": "0",
      "categories": null,
      "bookmark_url": "http://example.com/photo/11/509795882_preview_maxresdefault/",
      "photo_url": "http://example.com/PF.Base/file/pic/photo/2016/12/c266f348b34952c46eda13ced1daed00_1024.jpg"
    },
    {
      "is_liked": null,
      "user_id": "1",
      "photo_id": "10",
      "album_id": "0",
      "module_id": null,
      "group_id": "0",
      "privacy": "0",
      "privacy_comment": "0",
      "title": "photo_2",
      "time_stamp": "1481247906",
      "is_featured": "0",
      "is_sponsor": "0",
      "categories": null,
      "bookmark_url": "http://example.com/photo/10/509795882_preview_maxresdefault/",
      "photo_url": "http://example.com/PF.Base/file/pic/photo/2016/12/0a59cf98aef18ad53619bdec43d47c97_1024.jpg"
    }
  ],
  "messages": []
}
```

## POST /photo

_Share new photos_

### Example URI
> http://example.com/restful_api/photo

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| image[] | array | yes | Photos lists |
| val[description] | string | yes | Extra text to share with |
| val[user_id] | number | no | Support share photo on user profile |
| val[is_cover_photo] | boolean | no | Support upload cover photo |
| val[album_id] | number | no | Album id |
| val[privacy] | number | no | Privacy id |
| val[module_id] | string | no | Support share photo on item (pages/groups/event) |
| val[item_id] | number | no | Support share photo on item (pages/groups/event) |
| val[custom_pages_post_as_page] | number | no | Support share photo as page |

### Response
```
{
  "status": "success",
  "data": [
    {
      "is_liked": null,
      "user_id": "1",
      "photo_id": "14",
      "album_id": "0",
      "module_id": "event",
      "group_id": "1",
      "privacy": "0",
      "privacy_comment": "0",
      "title": "christmas-tree",
      "time_stamp": "1481888433",
      "is_featured": "0",
      "is_sponsor": "0",
      "categories": null,
      "bookmark_url": "http://example.com/photo/14/christmas-tree/",
      "photo_url": "http://example.com/PF.Base/file/pic/photo/2016/12/718d0e2a99dbe3091bf5e7734ed03098_1024.png"
    },
    {
      "is_liked": null,
      "user_id": "1",
      "photo_id": "15",
      "album_id": "0",
      "module_id": "event",
      "group_id": "1",
      "privacy": "0",
      "privacy_comment": "0",
      "title": "snowman",
      "time_stamp": "1481888433",
      "is_featured": "0",
      "is_sponsor": "0",
      "categories": null,
      "bookmark_url": "http://example.com/photo/15/snowman/",
      "photo_url": "http://example.com/PF.Base/file/pic/photo/2016/12/43a46fac997e887034c23c085ab0bbe8_1024.png"
    }
  ],
  "messages": [
    "Photos successfully uploaded."
  ]
}
```

## GET /photo/:id

_Get information of a specific photo_

### Example URI
> http://example.com/restful_api/photo/15

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Photo id|

### Response
```
{
  "status": "success",
  "data": {
    "is_liked": null,
    "user_id": "1",
    "photo_id": "15",
    "album_id": "0",
    "module_id": "event",
    "group_id": "1",
    "privacy": "0",
    "privacy_comment": "0",
    "title": "snowman",
    "time_stamp": "1481888433",
    "is_featured": "0",
    "is_sponsor": "0",
    "categories": null,
    "bookmark_url": "http://example.com/photo/15/snowman/",
    "photo_url": "http://example.com/PF.Base/file/pic/photo/2016/12/43a46fac997e887034c23c085ab0bbe8_1024.png"
  },
  "messages": []
}
```

## PUT /photo/:id

_Update information for a specific photo_

### Example URI
> http://example.com/restful_api/photo/15

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Photo id|
| val[privacy] | number | no | Privacy id |
| val[move_to] | number | no | Album id to move |
| val[description] | string | no | Photo description |
| val[category_id][] | array | no | List of category id |
| val[tag_list] | string | no | List of tags |
| val[mature] | boolean | no | Photo is mature photo or not |
| val[allow_download] | boolean | no | Can download photo or not |

### Response
```
{
  "status": "success",
  "data": {
    "is_liked": null,
    "user_id": "1",
    "photo_id": "15",
    "album_id": "1",
    "module_id": null,
    "group_id": "0",
    "privacy": "0",
    "privacy_comment": "0",
    "title": "snowman",
    "time_stamp": "1481189921",
    "is_featured": "0",
    "is_sponsor": "0",
    "categories": [
      {
        "0": "Comedy",
        "1": "http://example.com/photo/category/4/comedy/",
        "category_id": "4"
      }
    ],
    "bookmark_url": "http://example.com/15/snowman/",
    "photo_url": "http://example.com/PF.Base/file/pic/photo/2016/12/43a46fac997e887034c23c085ab0bbe8_1024.png"
  },
  "messages": [
    "Photo successfully updated."
  ]
}
```

## DELETE /photo/:id

_Delete a specific photo_

### Example URI
> http://example.com/restful_api/photo/15

### Parameters

| Parameter | Type | Require ? | Description |
| --------|---------|-------|----|
| id | number | yes | Photo id|

### Response
```
{
  "status": "success",
  "data": [],
  "messages": [
    "Photo successfully deleted."
  ]
}
```