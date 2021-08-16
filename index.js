let app = require ("express") ();
let server = require ("http").Server (app);
let io = require ("socket.io") (server);

let mongodb = require ("mongodb").MongoClient;
let request = require ("request");
let querystring = require ("querystring");
let bodyParser = require ("body-parser");
let jsonfile = require ("jsonfile");
let multer = require ("multer");
const filesystem = require ("fs");
const polly = require('./polly');
require('google-closure-library');

// require("./closure-library/closure/goog/bootstrap/nodejs");
require("./deps.js");
goog.provide("burnicen.readysetspell.node");

goog.require("goog.string");
goog.require("goog.Promise");
goog.require("burnicen.readysetspell.common");
goog.require("burnicen.readysetspell.server");

goog.asserts.ENABLE_ASSERTS = false; // Ð¾Ñ‚ÐºÐ»ÑŽÑ‡Ð°ÐµÐ¼ Ð°ÑÑÐµÑ€Ñ‚Ñ‹

/**
 * ÐºÐ¾Ñ€Ð½ÐµÐ²Ð°Ñ Ð¿Ð°Ð¿ÐºÐ° Ð¿Ñ€Ð¸Ð»Ð¾Ð¶ÐµÐ½Ð¸Ñ
 * @const
 * @type {string}
 */
let applicationRootFolder = "/var/www/gameServer";

/**
 * @implements {burnicen.readysetspell.common.Database}
 * @constructor
 */
burnicen.readysetspell.node.Database = function() {
  /**
   * Ñ…ÑÐ½Ð´Ð» ÑÐ¾ÐµÐ´Ð¸Ð½ÐµÐ½Ð¸Ñ Ñ MongoDB
   * @private
   */
  this.$connection;
}
/**
 * @return {goog.Promise}
 */
burnicen.readysetspell.node.Database.prototype.init = function() {
  return new goog.Promise(
    function(resolve, reject) {
      mongodb.connect("mongodb://127.0.0.1:27017/local", function(err, db) {
        if (err) {
          reject(err);
        } else {
          this.$connection = db;
          resolve();
        }
      }.bind(this));
    }, this);
}
burnicen.readysetspell.node.Database.prototype.load = function(collectionName, where, limit) {
  return new goog.Promise(function(resolve, reject) {
    /** @type {cursor} */
    let cursor = this.$connection.collection(collectionName).find(where);
    if (typeof limit !== "undefined") {
      cursor = cursor.limit(limit);
    }
    cursor.toArray(
      function(error, values) {
        if (error) {
          reject(error);
        }  else {
          resolve(values);
        }
      });
  }, this);
}
burnicen.readysetspell.node.Database.prototype.loadOne = function (collectionName, where)
{
  return new goog.Promise (function (resolve, reject)
  {
    this.$connection.collection (collectionName).findOne (where, {},
      function (error, value)
      {
        if (error)
          reject (error);
        else resolve (value);
      });
  }, this);
}
burnicen.readysetspell.node.Database.prototype.updateOne = function (collectionName, where, data)
{
  return new goog.Promise (function (resolve, reject)
  {
    this.$connection.collection (collectionName).findOneAndUpdate (where, {$set: data}, {returnOriginal: false},
      function (error, value)
      {
        if (error)
          reject (error);
        else resolve (value.value);
      });
  }, this);
}
burnicen.readysetspell.node.Database.prototype.delete = function (collectionName, where)
{
  return new goog.Promise (function (resolve, reject)
  {
    this.$connection.collection (collectionName).deleteMany (where, {},
      function (error, value)
      {
        if (error)
          reject (error);
        else resolve ();
      });
  }, this);
}
burnicen.readysetspell.node.Database.prototype.insert = function (collectionName, record)
{
  return new goog.Promise (function (resolve, reject)
  {
    this.$connection.collection (collectionName).insertOne (record, {},
      function (error, value)
      {
        if (error)
          reject (error);
        else resolve (record);
      });
  }, this);
}
/**
 * @implements {burnicen.readysetspell.common.Cache}
 * @constructor
 */
burnicen.readysetspell.node.Cache = function ()
{
}
/**
 * @return {goog.Promise}
 */
burnicen.readysetspell.node.Cache.prototype.init = function ()
{
  return goog.Promise.resolve ();
}
burnicen.readysetspell.node.Cache.prototype.getValue = function (key)
{
  //TODO: implement
  return null;
}
burnicen.readysetspell.node.Cache.prototype.setValue = function (key, value)
{
  //TODO: implement
}
/**
 * @implements {burnicen.readysetspell.common.FileSystem}
 * @constructor
 */
burnicen.readysetspell.node.FileSystem = function ()
{
}
/**
 * Ð¿Ð°Ð¿ÐºÐ° Ñ JSON-Ñ€ÐµÑÑƒÑ€ÑÐ°Ð¼Ð¸
 * @const
 * @private
 * @type {string}
 */
burnicen.readysetspell.node.FileSystem.JSON_RESOURCES_FOLDER = "./data/";
/**
 * @return {goog.Promise}
 */
burnicen.readysetspell.node.FileSystem.prototype.init = function ()
{
  return goog.Promise.resolve ();
}
burnicen.readysetspell.node.FileSystem.prototype.loadJSON = function (path)
{
  return new goog.Promise (function (resolve, reject)
  {
    jsonfile.readFile (goog.string.buildString (burnicen.readysetspell.node.FileSystem.JSON_RESOURCES_FOLDER, path),
      function (error, obj)
      {
        if (error)
          reject (error);
        else resolve (obj);
      });
  }, this);
}
burnicen.readysetspell.node.Logger = function ()
{
}
burnicen.readysetspell.node.Logger.prototype.logError = function (message)
{
  console.log (goog.string.buildString ("Error at ", new Date ().toLocaleString (), ": ", message));
}
burnicen.readysetspell.node.Logger.prototype.logInfo = function (message)
{
  console.log (goog.string.buildString ("Info at ", new Date ().toLocaleString (), ": ", message));
}
/**
 * @implements {burnicen.readysetspell.common.Translator}
 * @constructor
 */
burnicen.readysetspell.node.GoogleTranslator = function() {
  this.$service = require('google-translate')('zzzzzzzzzzzzzzzzzzzzzzzzzzzzz');
}
burnicen.readysetspell.node.GoogleTranslator.prototype.translate = function(message, language) {
  if (language == "en")
    return goog.Promise.resolve(message);
  else return new goog.Promise(function(resolve, reject) {
    this.$service.translate(message, 'en', language,
      function(err, translation) {
        if (!goog.isNull(err))
          reject(err);
        else resolve(translation["translatedText"]);
      });
  }, this);
}
burnicen.readysetspell.node.GoogleTranslator.prototype.translateStrings = function(strings, language) {
  if (language == "en")
    return goog.Promise.resolve(strings);
  else return new goog.Promise(function(resolve, reject) {
    this.$service.translate(strings, 'en', language,
      /**
       *
       * @param {Object?} err
       * @param {Array.<Object>?} translation
       */
      function(err, translation) {
        if (!goog.isNull(err))
          reject(err);
        else {
          if (goog.isNull(translation)) {
            reject("Google Translate returned null");
            return;
          }
          /** @type {Array.<string>} */
          let result = [];
          for (let i = 0; i < translation.length; ++i) {
            result.push(translation[i]["translatedText"]);
          }
          resolve(result);
        };
      });
  }, this);
}
/**
 * @implements {burnicen.readysetspell.common.Database}
 * @constructor
 */
burnicen.readysetspell.node.MemoryStorage = function ()
{
  /**
   * @type {Object.<string, Object>}
   * @private
   */
  this.$storage = {};
}
/**
 * Ð²Ñ€ÐµÐ¼Ñ Ð¶Ð¸Ð·Ð½Ð¸ Ð·Ð°Ð¿Ð¸ÑÐ¸ Ð² Ñ…Ñ€Ð°Ð½Ð¸Ð»Ð¸Ñ‰Ðµ, ÑÐµÐºÑƒÐ½Ð´
 * @const
 * @private
 * @type {number}
 */
burnicen.readysetspell.node.MemoryStorage.RECORD_TTL_SEC = 3600;
/**
 * Ð¿Ð¾Ð»Ðµ, Ð² ÐºÐ¾Ñ‚Ð¾Ñ€Ð¾Ð¼ ÑÐ¾Ñ…Ñ€Ð°Ð½ÑÐµÑ‚ÑÑ Ð²Ñ€ÐµÐ¼Ñ ÑÐ¾Ð·Ð´Ð°Ð½Ð¸Ñ Ð·Ð°Ð¿Ð¸ÑÐ¸
 * @const
 * @private
 * @type {string}
 */
burnicen.readysetspell.node.MemoryStorage.CREATION_TIME_FIELD = "_createdAt";
burnicen.readysetspell.node.MemoryStorage.prototype.load = function (collectionName, where)
{
  /** @type {Array.<Object>} */
  let result = [];
  /** @type {Array.<Object>} */
  let collection = /** @type {Array.<Object>} */ (goog.object.get (this.$storage, collectionName));
  if (typeof collection !== "undefined")
    for (let i = 0; i < collection.length; ++i)
    {
      /** @type {boolean} */
      let doesMatchWhereCondition = true;
      for (let whereField in where)
        doesMatchWhereCondition = doesMatchWhereCondition && (collection [i] [whereField] == where [whereField]);
      if (doesMatchWhereCondition)
        result.push (collection [i]);
    }
  return goog.Promise.resolve (result);
}
burnicen.readysetspell.node.MemoryStorage.prototype.loadOne = function (collectionName, where)
{
  /** @type {?Object} */
  let result = null;
  /** @type {Array.<Object>} */
  let collection = /** @type {Array.<Object>} */ (goog.object.get (this.$storage, collectionName));
  if (typeof collection !== "undefined")
    for (let i = 0; i < collection.length && goog.isNull (result); ++i)
    {
      /** @type {boolean} */
      let doesMatchWhereCondition = true;
      for (let whereField in where)
        doesMatchWhereCondition = doesMatchWhereCondition && (collection [i] [whereField] == where [whereField]);
      if (doesMatchWhereCondition)
        result = collection [i];
    }
  return goog.Promise.resolve (result);
}
burnicen.readysetspell.node.MemoryStorage.prototype.updateOne = function (collectionName, where, data)
{
  /** @type {?Object} */
  let result = null;
  /** @type {Array.<Object>} */
  let collection = /** @type {Array.<Object>} */ (goog.object.get (this.$storage, collectionName));
  if (typeof collection !== "undefined")
  {
    for (let i = 0; i < collection.length && goog.isNull (result); ++i)
    {
      /** @type {boolean} */
      let doesMatchWhereCondition = true;
      for (let whereField in where)
        doesMatchWhereCondition = doesMatchWhereCondition && (collection [i] [whereField] == where [whereField]);
      if (doesMatchWhereCondition)
      {
        for (let dataField in data)
          collection [i] [dataField] = data [dataField];
        result = collection [i];
      }
    }
    goog.object.set (this.$storage, collectionName, collection);
  }
  return goog.Promise.resolve (result);
}
burnicen.readysetspell.node.MemoryStorage.prototype.delete = function (collectionName, where)
{
  /** @type {?Object} */
  let result = null;
  /** @type {Array.<Object>} */
  let collection = /** @type {Array.<Object>} */ (goog.object.get (this.$storage, collectionName));
  if (typeof collection !== "undefined")
  {
    goog.array.removeAllIf (collection,
      function (record)
      {
        /** @type {boolean} */
        let doesMatchWhereCondition = true;
        for (let whereField in where)
          doesMatchWhereCondition = doesMatchWhereCondition && (record [whereField] == where [whereField]);
        return doesMatchWhereCondition;
      }, this);
    goog.object.set (this.$storage, collectionName, collection);
  }
  return goog.Promise.resolve ();
}
burnicen.readysetspell.node.MemoryStorage.prototype.insert = function (collectionName, record)
{
  /** @type {Array.<Object>} */
  let collection = /** @type {Array.<Object>} */ (goog.object.get (this.$storage, collectionName));
  if (typeof collection === "undefined")
    collection = [];

  /** @type {number} */
  let now = Date.now ();
  let itemsRemoved = goog.array.removeAllIf (collection,
    /**
     * @param {Object} element
     * @param {number} i
     */
    function (element, i)
    {
      return now - element [burnicen.readysetspell.node.MemoryStorage.CREATION_TIME_FIELD] >
        burnicen.readysetspell.node.MemoryStorage.RECORD_TTL_SEC * 1000;
    });

  if (itemsRemoved > 0)
    logger.logInfo (goog.string.buildString (itemsRemoved, " item(s) removed due to TTL expired"));

  record [burnicen.readysetspell.node.MemoryStorage.CREATION_TIME_FIELD] = now;
  collection.push (record);
  goog.object.set (this.$storage, collectionName, collection);
  return goog.Promise.resolve (record);
}

/**
 * @param {SocketIO} socket
 * @implements {burnicen.readysetspell.common.SocketIO}
 * @constructor
 */
burnicen.readysetspell.node.SocketIO = function (socket)
{
  this.$socket = socket;
}
burnicen.readysetspell.node.SocketIO.prototype.joinRoom = function (room)
{
  this.$socket.join (room);
}
burnicen.readysetspell.node.SocketIO.prototype.leaveRoom = function (room)
{
  this.$socket.leave (room);
}
burnicen.readysetspell.node.SocketIO.prototype.emitToRoom = function (room, event, param)
{
  io.to (room).emit (event, param);
}
burnicen.readysetspell.node.SocketIO.prototype.getIP = function ()
{
  /** @type {{address: string, port: number}} */
  let address = this.$socket.handshake.address;
  return address.address;
}

/** @type {burnicen.readysetspell.node.Logger} */
let logger = new burnicen.readysetspell.node.Logger ();
/** @type {burnicen.readysetspell.node.Database} */
let database = new burnicen.readysetspell.node.Database ();
/** @type {burnicen.readysetspell.node.Cache} */
let cache = new burnicen.readysetspell.node.Cache ();
/** @type {burnicen.readysetspell.node.FileSystem} */
let fs = new burnicen.readysetspell.node.FileSystem ();
/** @type {burnicen.readysetspell.node.Translator} */
let translator = new burnicen.readysetspell.node.GoogleTranslator ();
/** @type {burnicen.readysetspell.node.MemoryStorage} */
let memoryStorage = new burnicen.readysetspell.node.MemoryStorage ();

/**
 * Ð·Ð°Ð³Ñ€ÑƒÐ·Ð¸Ñ‚ÑŒ ÐºÐ°Ñ€Ñ‚Ð¸Ð½ÐºÑƒ
 * @param {string} uri
 * @param {string} filename - Ð¸Ð¼Ñ Ñ„Ð°Ð¹Ð»Ð° Ð´Ð»Ñ Ð·Ð°Ð³Ñ€ÑƒÐ·ÐºÐ¸
 */
let download = function (uri, filename)
{
  request.head (uri,
    function (err, res, body)
    {
      let r = request (uri).pipe (filesystem.createWriteStream (goog.string.buildString (applicationRootFolder, "/",
        burnicen.readysetspell.common.IMAGES_UPLOAD_FOLDER, "/", filename)));
      r.on ('close',
        function ()
        {
          logger.logInfo (goog.string.buildString ("Image '", filename, "' uploaded"));
        });
      r.on ('error',
        function (message)
        {
          logger.logError (message);
        });
    });
};

database.init ().then (
  function ()
  {
    return cache.init ();
  }, null).then (
  function ()
  {
    return burnicen.readysetspell.server.Server.getPromisedInstance (database, cache, fs, logger, translator, memoryStorage);
  }, null).then (
  function ()
  {
    logger.logInfo ("Server is up");

    app.use (bodyParser.json ());
    app.use (bodyParser.urlencoded ({extended: true}));

    /**
     * Ð¾Ð±Ñ€Ð°Ð±Ð¾Ñ‚Ð°Ñ‚ÑŒ ÐºÐ»Ð¸ÐµÐ½Ñ‚ÑÐºÐ¸Ð¹ HTTP-Ð·Ð°Ð¿Ñ€Ð¾Ñ
     * @param {string} method - HTTP-Ð¼ÐµÑ‚Ð¾Ð´ (GET, POST, UPDATE)
     * @param {string} query - Ð·Ð°Ð¿Ñ€Ð¾Ñ
     * @param {Object} parameters - Ð¿Ð°Ñ€Ð°Ð¼ÐµÑ‚Ñ€Ñ‹ Ð·Ð°Ð¿Ñ€Ð¾ÑÐ°
     * @param {Request} req - Ð¿Ð°Ñ€Ð°Ð¼ÐµÑ‚Ñ€Ñ‹ Ð·Ð°Ð¿Ñ€Ð¾ÑÐ°
     * @param {Response} res - Ð¾Ð±ÑŠÐµÐºÑ‚ Ð´Ð»Ñ Ð¾Ñ‚Ð²ÐµÑ‚Ð° ÐºÐ»Ð¸ÐµÐ½Ñ‚Ñƒ
     */
    let process = function (method, query, parameters, req, res)
    {
      burnicen.readysetspell.server.Server.getInstance ().RESTquery (method, query, parameters).then (
        /** @param {string} result */
        function (result)
        {
          res.send (result);
        },
        function (error)
        {
          if (goog.isNumber (error))
            res.status (error).send ("");
          else
          {
            /** @type {string} */
            let prefix = goog.string.buildString (method, " of ", query, ": ");
            if (error)
              if (error.stack)
                logger.logError (goog.string.buildString (prefix, error.stack));
              else logger.logError (goog.string.buildString (prefix, error));
            else logger.logError (goog.string.buildString (prefix, "unknown error"));
            res.status (500).send ("");
          }
        }, this);
    };
    /**
     * Ð¾Ð±Ñ€Ð°Ð±Ð¾Ñ‚Ð°Ñ‚ÑŒ ÐºÐ»Ð¸ÐµÐ½Ñ‚ÑÐºÐ¸Ð¹ SocketIO-Ð·Ð°Ð¿Ñ€Ð¾Ñ
     * @param {SocketIO} socket
     * @param {string} method - HTTP-Ð¼ÐµÑ‚Ð¾Ð´ (GET, POST, UPDATE)
     * @param {string} query - Ð·Ð°Ð¿Ñ€Ð¾Ñ
     * @param {Object} parameters - Ð¿Ð°Ñ€Ð°Ð¼ÐµÑ‚Ñ€Ñ‹ Ð·Ð°Ð¿Ñ€Ð¾ÑÐ°
     * @param {string} responseSignal - ÑƒÐ½Ð¸ÐºÐ°Ð»ÑŒÐ¾Ðµ Ð¸Ð¼Ñ ÑÐ¸Ð³Ð½Ð°Ð»Ð° Ð´Ð»Ñ Ð²Ð¾Ð·Ð²Ñ€Ð°Ñ‚Ð° Ð·Ð½Ð°Ñ‡ÐµÐ½Ð¸Ñ Ð²Ñ‹Ð¿Ð¾Ð»Ð½ÐµÐ½Ð¸Ñ Ð·Ð°Ð¿Ñ€Ð¾ÑÐ° ÐºÐ»Ð¸ÐµÐ½Ñ‚Ñƒ
     */
    let processSocket = function (socket, method, query, parameters, responseSignal)
    {
        const server = burnicen.readysetspell.server.Server.getInstance();
        const responsePromise = server.RESTquery(method, query, parameters, new burnicen.readysetspell.node.SocketIO(socket));
        responsePromise.then(
            /** @param {string} result */
            function (result) {
                if ('getStudentWordList' === query) {
                    /** @type {Array.<burnicen.readysetspell.common.WORD_LIST_ITEM>} */
                    const words = JSON.parse(result);
                    goog.array.forEach(words, function (/** burnicen.readysetspell.common.WORD_LIST_ITEM */ word) {
                      synthesizeSpeech(word.word.toLowerCase(), goog.string.buildString(applicationRootFolder, '/res/sounds/polly'))
                    });
                }
                socket.emit(responseSignal, result, 200);
            },
        function (error)
        {
          if (goog.isNumber (error))
          {
            logger.logError (goog.string.buildString (method, " of ", query, " caused error: ", error));
            socket.emit (responseSignal, goog.string.buildString ("Server error: ", error), 500);
          }
          else
          {
            /** @type {string} */
            let prefix = goog.string.buildString (method, " of ", query, " caused error: ");
            if (error)
              if (error.stack)
                logger.logError (goog.string.buildString (prefix, error.stack));
              else logger.logError (goog.string.buildString (prefix, error));
            else logger.logError (goog.string.buildString (prefix, "unknown error"));
            socket.emit (responseSignal, error, 500);
          }
        }, this);
    };
    /**
     * Ð´Ð¾Ð±Ð°Ð²Ð¸Ñ‚ÑŒ Ð¿Ð°Ñ€Ð°Ð¼ÐµÑ‚Ñ€Ñ‹ Ð¸Ð· Ñ‚ÐµÐ»Ð° Ð·Ð°Ð¿Ñ€Ð¾ÑÐ° Ð² Ð¾Ð±ÑŠÐµÐºÑ‚ Ñ Ð¿Ð°Ñ€Ð°Ð¼ÐµÑ‚Ñ€Ð°Ð¼Ð¸ URL
     * @param {Object} requestParams - Ð¿Ð°Ñ€Ð°Ð¼ÐµÑ‚Ñ€Ñ‹ URL
     * @param {Object} body - Ð¿Ð°Ñ€Ð°Ð¼ÐµÑ‚Ñ€Ñ‹ Ð² Ñ‚ÐµÐ»Ðµ Ð·Ð°Ð¿Ñ€Ð¾ÑÐ°
     */
    let mergeBodyIntoRequestParams = function (requestParams, body)
    {
      /** @type {Array.<string>} */
      let parameters = Object.getOwnPropertyNames (body);
      for (let i = 0; i < parameters.length; ++i)
      {
        /** @type {string} */
        let p = parameters [i];
        requestParams [p] = body [p];
      }
    }
    app.get ('/:query',
      function (req, res)
      {
        process ("GET", req.params ["query"], req.query, req, res);
      });
    app.put ('/image',
      function (req, res)
      {
        mergeBodyIntoRequestParams (req.query, req.body);
        download (req.query ["uri"], req.query ["filename"]);
        res.status (200).send ("");
      });
    app.put ('/:query',
      function (req, res)
      {
        mergeBodyIntoRequestParams (req.query, req.body);
        process ("PUT", req.params ["query"], req.query, req, res);
      });
    app.delete ('/:query',
      function (req, res)
      {
        mergeBodyIntoRequestParams (req.query, req.body);
        process ("DELETE", req.params ["query"], req.query, req, res);
      });

    io.on ("connection",
      function (socket)
      {
        //logger.logInfo ("Client connected via Socket.IO");
        let onevent = socket.onevent;
        socket.onevent =
          function (packet)
          {
            let args = packet.data || [];
            onevent.call (this, packet); // original call
            packet.data = ["*"].concat (args);
            onevent.call (this, packet); // additional call to catch-all
          };

        socket.on ("*",
          function (query, params, method, responseSignal)
          {
            processSocket (socket, method, query, params, responseSignal);
          });
      });
    io.on ("connection",
      function (socket)
      {
        //logger.logInfo ("Client disconnected via Socket.IO");
      });

    let storage = multer.diskStorage ({
      destination: function (req, file, cb) {
        cb (null, "/var/www/gameServer/uploads")
      },
      filename: function (req, file, cb) {
        cb (null, file.originalname);
      }
    });
    let upload = multer({ storage: storage });
    let type = upload.single ('file');

    app.post('/upload', type, function (req, res)
    {
      logger.logInfo ("Something started uploading...");
    });

    app.post ('/:query',
      function (req, res)
      {
        mergeBodyIntoRequestParams (req.query, req.body);
        process ("POST", req.params ["query"], req.query, req, res);
      });

    server.listen (9999, function ()
    {
      let host = server.address ().address;
      let port = server.address ().port;

      logger.logInfo (goog.string.buildString ("Ready Set Spell is listening port ", port));
    });
  },
  function (error)
  {
    logger.logError (error);
  });

function synthesizeSpeech(word, path) {
    const outputFormat = 'mp3';
    const voiceId = 'Joanna';
    const params = {
        'Text': word,
        'OutputFormat': outputFormat,
        'VoiceId': voiceId
    };

    const filePath = goog.string.buildString(path, '/', voiceId.toLowerCase(), '/', word.toLowerCase(), '.', outputFormat.toLowerCase());
    filesystem.exists(filePath, function (exists) {
        if (!exists) {
            polly.synthesizeSpeech(params, function (error, data) {
                if (error) {
                    logger.logError("Error 1: " + error.code)
                } else if (data) {
                    if (data.AudioStream instanceof Buffer) {
                        filesystem.writeFile(filePath, data.AudioStream, function (err) {
                            if (err) {
                                logger.logError("Error 2: " + err);
                                return;
                            }
                        })
                    }
                }
            })
        }
    });
}