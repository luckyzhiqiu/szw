//豪杰
struct Hero
{
	////////////////////////////////////////////////
	int32 heroIndex:1;//豪杰位置（对应json.hero下标）
	int32 heroID:2;//豪杰ID（对应hero.json）
	int8 useSign:3;//上阵标记:0=否;1=是;
	string name:4;//名称
	int16 head:5=1;//头像
	int16 level:6=1;//等级
	int16 promotion:7=1;//提拔等级（爵位）
	string specialty:8;//特长
	////////////////////////////////////////////////
	int32 levelExp:9;//等级经验
	int32 skillExp:10;//技能经验
	int32 growsExp:11;//资质经验
	////////////////////////////////////////////////
	int32 strengthGrows:12;//武力资质
	int32 wisdomGrows:13;//智力资质
	int32 charmGrows:14;//魅力资质
	int32 politicsGrows:15;//政治资质
	////////////////////////////////////////////////
	array<int32> skill:16;//技能数组//[1,2]
	////////////////////////////////////////////////
	//属性总和（包括加成）
	int32 strengthTotal:17;//武力
	int32 wisdomTotal:18;//智力
	int32 charmTotal:19;//魅力
	int32 politicsTotal:20;//政治
	////////////////////////////////////////////////
	//属性
	int32 strength:21;//武力
	int32 wisdom:22;//智力
	int32 charm:23;//魅力
	int32 politics:24;//政治
	////////////////////////////////////////////////
	//点数加成[丹药]
	int32 strengthAdd2:25;//武力
	int32 wisdomAdd2:26;//智力
	int32 charmAdd2:27;//魅力
	int32 politicsAdd2:28;//政治
	////////////////////////////////////////////////
	//点数加成[珍宝]
	int32 strengthAdd3:46;//武力
	int32 wisdomAdd3:47;//智力
	int32 charmAdd3:48;//魅力
	int32 politicsAdd3:49;//政治
	//比例加成[珍宝]
	int32 strengthPercent3:50;//武力
	int32 wisdomPercent3:51;//智力
	int32 charmPercent3:52;//魅力
	int32 politicsPercent3:53;//政治
	////////////////////////////////////////////////
	//点数加成[技能]
	int32 strengthAdd:29;//武力
	int32 wisdomAdd:30;//智力
	int32 charmAdd:31;//魅力
	int32 politicsAdd:32;//政治
	//比例加成[技能]
	int32 strengthPercent:33;//武力
	int32 wisdomPercent:34;//智力
	int32 charmPercent:35;//魅力
	int32 politicsPercent:36;//政治
	////////////////////////////////////////////////
	//经营势力比例加成[技能]
	int32 businessPercent:37;//商业收益百分比提升
	int32 farmPercent:38;//农业收益百分比提升
	int32 recruitPercent:39;//征兵收益百分比提升
	//势力比例加成[技能]
	int32 strengthAllPercent:42;//武力
	int32 wisdomAllPercent:43;//智力
	int32 charmAllPercent:44;//魅力
	int32 politicsAllPercent:45;//政治
	////////////////////////////////////////////////
	//BOSS战暴击加成[技能]
	int32 critRate:40;//BOSS战暴击率
	int32 crit:41;//BOSS战暴击系数
	////////////////////////////////////////////////
	array<int32> gemIndexArr:54;//珍宝索引数组（对应gemArr中的索引），格式：push(gemIndexArr,gemIndex);
}

//红颜
struct Wife
{
	int32 wifeID:1;//红颜ID
	int32 beauty:2;//红颜魅力
	int32 intimate:3;//亲密度
	int32 exp:4;//经验
	array<int32> skill:5;//技能列表//[skillID1,skillID2]
	int32 childrenNum:6;//生孩数
}

//红颜随机宠幸
struct WifeRandomFuck
{
	double beginTime:1;//开始时间
}

//剧情
struct Story
{
	string date:1="2017-8-26";//日期
	dict<int8> heroIndexMap:2;//关卡BOSS出战豪杰字典//heroIndexMap[heroIndex]=1;
	dict<int8> recoverHeroIndexMap:3;//关卡BOSS出战豪杰恢复字典//recoverHeroIndexMap[heroIndex]=recoverCount;
}

//经营豪杰上阵
struct MakeResHeroUp
{
	int32 heroIndex:1=-1;//豪杰列表位置
	double beginTime:2;//上阵开始时间（毫秒）
}

//经营
struct MakeRes
{
	double beginTime:1;//资源增长开始时间戳（毫秒）
	int32 recvCount:2;//可收成轮数
	int32 dt:3=60;//每轮间隔时间（秒）
	array<MakeResHeroUp> hero:4;//上阵豪杰列表（初始化5个元素）
}

//经营:军营
struct Battle
{
	array<int32> hero:1;//上阵豪杰index数组,空位置值为-1（初始化5个元素）//[-1,-1,-1,-1,-1]
}

//成就
struct Achievement
{
	dict<int8> achievement:1;//成就领取记录（key为领取过的成就ID，val=1）
	string loginDate:2="2017-9-4";//最后登录日期
	int32 loginDayCount:3;//累计登录天数
	int32 makeMoney:4;//经营商业次数
	int32 makeFood:5;//经营农业次数
	int32 makeSoldier:6;//征兵次数
	int32 randomFuckCount:7;//随机宠幸次数
	int32 goldFuckCount:16;//元宝宠幸次数
	int32 visitCount:8;//完成寻访次数
	int32 waterCount:9;//议政次数
	int32 kneeCount:10;//膜拜次数
	int32 helloCount:11;//请安次数
	int32 childrenCount:12;//子女数
	int32 workCount:13;//政务次数
	int32 marryCount:14;//联姻次数
	int32 unionCount:15;//联盟建设（捐献）
	dict<int32> useItemMap:17;//道具使用次数字典，格式：useItemMap[itemID]=useCount;
	int32 hitPrisonerCount:18;//惩戒犯人次数
	int32 upChild:19;//培养子女次数
	dict<int8> lotteryItemMap:20;//抽签已抽物品字典，格式：lotteryItemMap[itemID]=0;
}

//主线任务（计数对应成就Achievement）
struct MainTask
{
	int32 id:1;//已经领取日常任务ID（按顺序领取）
}

//日常任务
struct DailyTask
{
	dict<int8> idMap:1;//成就领取记录（key为领取过的成就ID，val=1）
	string date:2="2017-9-4";//刷新日期
	int32 active:3;//活跃点数
	int32 activeCount:4;//活跃点数领取次数
	int32 heroUpCount:5;//豪杰升级次数
	int32 makeMoneyCount:6;//商业经营次数
	int32 makeFoodCount:7;//农业经营次数
	int32 makeSoldierCount:8;//征兵次数
	int32 barrierCount:9;//通关次数
	int32 randomFuckCount:10;//翻牌子次数（随机宠幸次数）
	int32 upChild:17;//培养子女次数
	int32 useItem9_12:18;//使用资质果次数
	int32 waterCount:19;//议政厅论战次数
	int32 visitCount:20;//微服私访次数
	// int32 unionCount:21;//联盟捐献次数
	int32 helloCount:22;//皇宫请安次数
	int32 buyCount:23;//商城购物次数
	int32 monthCard:24;//领取月卡
	int32 yearCard:25;//领取年卡

	//膜拜
	int8 knee1:11;//膜拜势力榜次数
	int8 knee2:12;//膜拜亲密榜次数
	int8 knee3:13;//膜拜关卡榜次数
	//
	dict<int8> dailyTaskRewardIDMap:14;//（key为领取过的成就ID，val=1）
	
	//活动充值奖励
	int32 rechargeOneDay:15;//每日充值（分）
	dict<int8> rechargeRewardIDMap:16;//充值奖励领取标记列表（对应eventPay.excel中类型1）
	
	//联盟
	dict<int8> unionBuildMap:26;//联盟每日建设记录字典，格式：unionBuildMap[unionID]=allianceBuildID;其中allianceBuildID对应allianceBuild表
	dict<int32> unionExchangeItemIDMap:27;//联盟兑换物品次数字典，格式：unionExchangeItemIDMap[itemID]=count;
	dict<int8> unionBossHeroMap:28;//联盟BOSS豪杰上阵字典，heroIndex存在时代表上阵过//unionBossHeroMap[heroIndex]=1;
	dict<int8> unionBossHeroRecoveMap:29;//联盟BOSS豪杰恢复字典，heroIndex存在时代表上阵过//unionBossHeroRecoveMap[heroIndex]=1;
	dict<int8> reqUnionMap:30;//申请加入联盟字典（当前申请历史）
	
	//寻访系统
	int32 searchGodTellingFree:31;//卜卦免费使用次数
	int32 searchGodTelling:32;//卜卦元宝使用次数
	
	//宴会
	int32 partyCount:33;//赴宴次数
	int32 partyFlushResCount:34;//刷新兑换物品次数
}

//限时任务（限时领奖）
struct LimitTask
{
	double bt:5;//阶段开始时间（秒）,对应阶段开始时间
	dict<int8> idMap:1;//领取记录（key为领取过的ID，val=1）
	int32 useGold:2;//元宝消耗（底层计算）
	int32 useSoldier:3;//士兵消耗（底层计算）
	int32 useMoney:4;//银两消耗（底层计算）
	int32 loginDay:6;//登录天数（底层计算）
	int32 workCount:8;//政务次数
	int32 makeMoneyCount:9;//经营商产
	int32 makeFoodCount:10;//经营农产
	int32 makeSoldierCount:11;//征兵次数
	int32 addIntimate:12;//亲密度涨幅（底层计算）
	///////////////////////////////////////////////////////////////////////
	//2017-11-13增加
	int32 useItem9_12:13;//使用资质果次数
	int32 powerAdd:14;//势力涨幅（底层计算）
	int32 useItem4:15;//精力丸消耗
	int32 useItem71:16;//体力丹消耗
	int32 visitCount:17;//寻访次数
	int32 useItem50:18;//挑战书消耗
	int32 pairCount:19;//联姻次数
	int32 killWorldBoss:20;//击杀世界boss次数
	int32 hitPrisonerCount:21;//惩戒犯人次数
	int32 waterScoreAdd:22;//议政厅权威涨幅（底层计算）
	int32 charmAdd:23;//势力魅力值涨幅（底层计算）
	int32 beautyAdd:24;//红颜魅力值涨幅（底层计算）
	///////////////////////////////////////////////////////////////////////
	//2018-01-03增加
	double unionBossHit:25;//对联盟BOSS造成伤害累计
	int32 killUnionBoss:26;//击杀联盟boss次数
	///////////////////////////////////////////////////////////////////////
	//活动充值奖励
	string rechargeDate:101;//充值日期字符串，格式：2017-11-10
	int32 rechargeDays:102;//充值累计天数
	int32 recharge:103;//活动期间充值额（分）
	dict<int8> rechargeRewardIDMap:104;//充值奖励领取标记列表（对应eventPay.excel中类型2、3）
}

//VIP
struct VIP
{
	int32 makeMoneyCount:1=0;//十倍商产每日次数（经营商产时按概率1触发十倍商产）
	int32 heroUpCount:2=0;//连升三级每日次数（豪杰升级概率2触发）
	int32 fuckCount:3=0;//红颜赐福每日次数（红颜宠幸概率5触发5倍经验）
	int32 childCount:4=0;//天资聪慧每日次数（概率3触发子嗣培养3倍经验）
	int32 skyCount:5=0;//天赐元宝每日次数
	dict<int8> vipGetItemMap:6;//VIP购买物品礼包字典，格式vipGetItemMap[vipID]=0;
}

//红颜加成
struct WifePlus
{
	//红颜属性加成
	int32 strength:1;//主角武力提升
	int32 wisdom:2;//主角智力提升
	int32 charm:3;//主角魅力提升
	int32 politics:4;//主角政治提升
	//红颜属性资质加成
	int32 strengthGrows:5;//主角武力资质提升
	int32 wisdomGrows:6;//主角智力资质提升
	int32 charmGrows:7;//主角魅力资质提升
	int32 politicsGrows:8;//主角政治资质提升
	//势力比例加成
	int32 strengthPercent:9;//势力武力万分比提升
	int32 wisdomPercent:10;//势力智力万分比提升
	int32 charmPercent:11;//势力魅力万分比提升
	int32 politicsPercent:12;//势力政治万分比提升
	//势力经营比例加成
	int32 businessPercent:13;//势力商业收益万分比提升
	int32 farmPercent:14;//势力农业收益万分比提升
	int32 recruitPercent:15;//势力征兵收益万分比提升
}

//论战豪杰信息
struct WaterBattleHero
{
	int32 hp:1;//血量上限=智+武+政+魅
	int32 attack:2;//攻击力=资质*100//战斗时：+(rand()%201)-100
	int32 attackC:3=10000;//攻击力倍数
	int32 critRate:4;//爆击率
	int32 crit:5;//爆击系数
	int32 heroID:6;//豪杰ID（塔战时指怪物ID）
	int32 level:7;//豪杰等级
	int16 promotion:12=1;//提拔等级（爵位）
	int32 strengthGrows:8;//武力资质
	int32 wisdomGrows:9;//智力资质
	int32 charmGrows:10;//魅力资质
	int32 politicsGrows:11;//政治资质
}

//论战（议政）
struct WaterBattle
{
	double hp:2;//我方血量
	WaterBattleHero me:3;//我方豪杰
	array<int32> enemyHeroIDArr:4;//随机3个敌方豪杰ID
	array<int16> enemyHeroPromotionArr:20;//提拔等级数组（爵位），对应enemyHeroIDArr
	int8 buyBuff:5=0;//购买BUFF ID
	array<int32> buffArr:6;//可购买BUFF数组
	int32 battleCount:7;//对战次数
	int32 heroIndex:8;//我方豪杰位置
	//string enemyNicknname:10;//敌方昵称
	int32 enemyUserID:11;//敌方userID
	int32 enemyUserHead:18;//敌方头像
	int32 enemyUserLevel:19;//敌方等级
	int32 enemyFashionID:21;//敌方时装
	int32 winCount:12;//击败敌方豪杰数量
	int32 enemyHeroCount:13;//敌方豪杰总数量
	//累积加成
	int32 attackPlus:14;//攻击加成
	int32 skillPlus:15;//技能加成
	//
	dict<int8> useHeroIndexMap:16;//豪杰使用字典（记录：反击、挑战、追杀所使用的heroIndex）//useHeroIndexMap[heroIndex]=1;
	int8 type:17;//类型：1=议政;2=追杀（输入玩家ID）;3=反击（仇人列表）;4=挑战（日榜）;
	//
	dict<int8> normalUseHeroIndexMap:22;//豪杰使用字典（记录：正常议政所使用的heroIndex）//normalUseHeroIndexMap[heroIndex]=1;
}

//论战仇人信息
struct WaterBattleEnemyInfo
{
	int32 userID:1;//仇人userID
	double time:2;//被打时间（毫秒）
	// string enemyNickname:3;//敌方玩家名称
	// int32 level:4;//官阶（品级）
	// int32 power:5;//势力
	// int32 waterBattleScore:6;//论战总积分
}

//论战被打记录信息
struct WaterBattleHitInfo
{
	int32 userID:1;//仇人userID
	double time:2;//被打时间（毫秒）
	// string enemyNickname:3;//敌方玩家名称
	string enemyHeroID:4;//敌方豪杰ID
	int32 winCount:5;//战胜我方N名豪杰
	int32 battleScore:6;//对战结束得分扣分情况
}

//论战扩展
struct WaterBattleExt
{
	array<WaterBattleHero> enemyHeroArr:1;//敌方豪杰数组
	array<WaterBattleEnemyInfo> enemyArr:2;//仇人数组
	array<WaterBattleHitInfo> hitArr:3;//被打数组
	int32 hitUnreadCount:4;//被打数组未读计数
}

//政务
struct Work
{
	double beginTime:1;//开始时间戳（毫秒）
	int32 politicsID:2;//政务奖励ID
}

//皇宫
struct KingCity
{
	int8 sign:1;//请安（领取标记）：0=未领取；1=已领取；
}

//被提亲信息
struct PairReq
{
	string name:1;//孩子姓名
	string nickname:2;//玩家昵称
	int32 grows:3;//资质（对应科举身份）
	int32 num:4;//孩子属性
	int8 sex:5;//性别：0=女；1=男；
	int32 skin:6;//形象ID
	int32 childID:7;//孩子ID
	int32 userID:8;//用户ID
}

//子嗣（孩子）信息
struct Child
{
	int32 id:13;//子嗣ID
	int32 skin:14;//形象ID
	int32 level:1=1;//等级
	string name:2;//姓名
	int32 grows:3;//资质（对应科举身份）
	int32 strengthTotal:4;//武力
	int32 wisdomTotal:5;//智力
	int32 charmTotal:6;//魅力
	int32 politicsTotal:7;//政治
	int32 wifeID:8;//红颜ID（母亲）
	double bt:9;//元气恢复开始时间（秒）
	int32 exp:10;//经验
	int8 sex:11;//性别：0=女；1=男；
	//提亲
	int64 targetUserID:12=-1;//提亲目标：-1=未提亲；-2=向玩家提亲被拒绝；0=向全服玩家提亲；大于0=向玩家提亲；
	double genTime:15;//提亲时间（秒）
	string targetNickname:16;//目标玩家昵称
	//招新
	array<PairReq> globalReqArr:17;//全服招亲刷新的孩子列表（最多3个）
	double globalReqTime:18;//全服招亲刷新时间（秒） 
}

//子嗣系统
struct ChildSys
{
	array<Child> children:1;//孩子数组
	int32 childMaxCount:2=2;//孩子席位最大值（2~7）
	int32 childID:3;//保存最后一次分配的子嗣ID
}

//联姻孩子信息
struct Pair
{
	//我方孩子
	string name:1;//我方孩子姓名
	int32 grows:2;//我方孩子资质（对应科举身份）
	int32 num:3;//我方孩子属性
	int8 sex:9;//我方孩子性别：0=女；1=男；
	//对方孩子
	string name2:4;//对方孩子姓名
	int32 grows2:5;//对方孩子资质（对应科举身份）
	int32 num2:6;//对方孩子属性
	int8 sex2:10;//对方性别：0=女；1=男；
	//对方信息
	string nickname:7;//对方玩家昵称
	//日期
	double time:8;//发生日期（秒）
}

//联姻系统
struct PairSys
{
	array<Child> children:1;//未婚孩子数组
	array<Pair> pairArr:2;//联姻孩子数组
	array<PairReq> pairReqArr:3;//被提亲信息数组
	array<Pair> AgreeArr:4;//被同意数组，扫描后要清空
}

//开服活动百废待兴
struct BuildActivity
{
	dict<int32> buyItemIDMap:1;//购买物品次数字典//buyItemIDMap[itemID]=count;
	dict<int32> exchangeItemIDMap:2;//兑换物品次数字典//buyItemIDMap[itemID]=count;
	int8 buildRewardSign:3;//建设领奖标记：0=未领取；1=已领取；
}

//牢房犯人
struct Prisoner
{
	int32 id:1;//犯人ID
	int32 hp:2;//血量
	int32 hpMax:7=2800;//血量最大值
	int32 def:3;//心理防线值
	int32 defMax:6=4200;//心理防线最大值
	int32 check:4;//审查次数，范围：0~8
	int32 gemIndex:5=-1;//珍宝索引（对应珍宝数组）
}

//珍宝属性
struct GemAtt
{
	int32 id:1;//属性ID，对应json_table/treasurePool.json中id
	int8 open:2;//解锁标记：0=否；1=是；
}

//珍宝
struct Gem
{
	int32 id:1;//珍宝ID，对应json_table/treasure.json中id
	array<GemAtt> attArr:2;//属性数组
}

//珍宝系统
struct GemSys
{
	array<Gem> gemArr:4;//珍宝数组
}

//牢房系统
struct Prison
{
	array<Prisoner> prisonerArr:1;//犯人数组（血量每天重置）
	int32 hitCount:2;//打犯人次数（每天清0）
	int32 hitPercent:3;//严刑拷打（万份比加成）
}

//寻访角色信息
struct SearchRole
{
	int32 friend:1;//熟悉度
}

//寻访系统
struct Search
{
	dict<SearchRole> roleMap:1;//寻访角色信息字典roleMap[searchCharacterID]=new SearchRole();
	double updateTime:2;//更新时间（秒）
	//卜卦
	int32 searchCount:3;//高缘分寻访次数
	int32 searchBuildingID:4;//高缘分寻访建筑
	int32 searchCharacterID:5;//高缘分寻访人物
}

//白夜缉凶系统（世界BOSS）
struct WorldBoss
{
	//日场
	int32 monCount:1;//当前阶段打怪数量（达到该阶段打怪最大值阶段+1）
	int32 monCountMax:9;//当前阶段打怪数量最大值
	int32 stage:2;//打怪阶段
	array<int8> findArr:3;//发现状态数组（元素数量3，定义：0=未发现；1=已发现）
	array<int8> findTypeArr:4;//发现类型数组（元素数量3，定义：1=发现线索；2=发现小怪）
	int8 status:5;//阶段：0=初始阶段（初始化发现数组）；1=选择发现阶段；2=发现小怪阶段；3=犒赏完成阶段（犒赏之后日场结束）
	int32 monHP:6;//小怪血量
	dict<int8> monHeroMap:7;//日场豪杰上阵字典，heroIndex存在时代表上阵过//monHeroMap[heroIndex]=1;
	dict<int8> monHeroRecoveMap:8;//日场豪杰恢复字典，heroIndex存在时代表恢复过//monHeroRecoveMap[heroIndex]=1;
	
	//夜场
	dict<int8> bossHeroMap:20;//夜场豪杰上阵字典，heroIndex存在时代表上阵过//bossHeroMap[heroIndex]=1;
	dict<int8> bossHeroRecoveMap:21;//夜场豪杰恢复字典，heroIndex存在时代表上阵过//bossHeroRecoveMap[heroIndex]=1;
	
	//兑换
	dict<int8> itemMap:30;//兑换信息字典//itemMap[itemID]=count;
}

//VIP福利
struct VipWeal
{
	dict<int8> vipMap:1;//领取字典，格式：vipMap[level]=0;
}

//新手引导
struct Guide
{
	dict<int8> sysIDMap:1;//已完成引导的系统ID，格式：sysIDMap[sysID]=0;
}

//我家装饰系统
struct Decorate
{
	dict<int32> map:1;//装饰字典，格式：map[decorateType]=itemID;
}

//7天签到
struct SomeDayReward
{
	string date:1;//领取日期，格式：2017-12-25
	int32 count:2;//领取次数
	dict<int8> getMap:3;//领取字典，格式：getMap[天数]=0;
}

//cdkey
struct CDKey
{
	dict<int8> cdkeyMap:1;//使用过的有效CDKEY列表，格式：cdkeyMap[cdkey]=0;
}

//书院豪杰信息
struct BookHouseHero
{
	int32 heroIndex:1;//豪杰位置
	double bt:2;//开始时间戳（秒）
	double doubleExp:3;//双倍经验标记
}

//书院
struct BookHouse
{
	dict<int32> heroStudyCountMap:1;//豪杰修行次数字典，格式：heroStudyCountMap[heroID]=studyCount
	int32 seatCount:2=1;//坐席数量
	array<BookHouseHero> heroArr:3;//书院豪杰数组（代表坐进去的豪杰）
}

//为官之道
struct MyRoad
{
	int32 id:1;//最后打通的关卡ID
	dict<int8> getMap:2;//领取奖励标记字典，格式：getMap[关卡id]=0;
	dict<int8> heroIndexMap:3;//上阵豪杰index数组
	double bossHP:4;//BOSS血量
	int8 getHero:5;//领取豪杰标记
}

//过关斩将（塔战）
struct TowerBattle
{
	string date:1="2017-8-26";//当前关卡开始日期
	double hp:2;//我方血量
	WaterBattleHero me:3;//我方豪杰
	array<int32> enemyHeroIDArr:4;//随机3个敌方豪杰ID
	array<int16> enemyHeroPromotionArr:20;//提拔等级数组（爵位），对应enemyHeroIDArr
	int8 buyBuff:5=0;//购买BUFF ID
	array<int32> buffArr:6;//可购买BUFF数组
	int32 battleCount:7;//对战次数（用于记录当前豪杰连胜次数）
	int32 heroIndex:8;//我方豪杰位置
	//
	int32 winCount:12;//击败敌方豪杰数量
	int32 enemyHeroCount:13;//敌方豪杰总数量
	//累积加成
	int32 attackPlus:14;//攻击加成
	int32 skillPlus:15;//技能加成
	//
	dict<int8> useHeroIndexMap:22;//豪杰使用字典（每打过10关清空此字典），格式：useHeroIndexMap[heroIndex]=1;
}

//过关斩将扩展（塔战）
struct TowerBattleExt
{
	array<WaterBattleHero> enemyHeroArr:1;//敌方豪杰数组
}

//百姓进贡
struct Tribute
{
	double getTime:1;//领取时间（秒）
}

//宴会信息
struct PartyInfo
{
	int32 userID:1;//主办方userID
	int32 type:2;//类型：0=家宴；1=官宴（必须公开）；
	string nickname:3;//玩家昵称
}

//宴会历史信息
struct PartyHistroy
{
	string genTime:1;//开始时间，格式：2018-01-30 15:18:30
	int32 type:2;//类型：0=家宴；1=官宴（必须公开）；
	int32 joinNum:3;//赴宴人数
	int32 breakNum:4;//捣乱人数
	int32 partyScore:5;//宴会分数
	int32 partyMoney:6;//宴会积分
}

//宴会捣乱历史信息
struct PartyBreakHistroy
{
	int32 userID:1;//座位上对应的userID
	string nickname:2;//昵称
	string genTime:3;//就座时间，格式：2018-01-30 15:18:30
	int32 score:4;//影响分数：参加为正，捣乱为负
}

//宴会座位
struct PartySeat
{
	int32 userID:1;//座位上对应的userID，为0时代表此位置空
	string nickname:2;//昵称
	int32 head:3;//头像
	int32 level:4;//等级
	string genTime:5;//就座时间，格式：2018-01-30 15:18:30
	int32 score:6;//影响分数：参加为正，捣乱为负
	int32 joinType:7;//参加类型：对应json_table/dinnerGo.json
	int32 vip:8;//VIP
}

//宴会
struct Party
{
	int32 score:1;//分数（主办方结算时所得分数）
	array<PartySeat> seatArr:8;//自己宴会的座位数组（必须初始化为user表中partyNum个位置）
	///////////////////////////////////////////////////
	//刷出其它人的宴会数组
	double partyArrUpdateTime:2;//刷新时间（秒）
	array<PartyInfo> partyArr:3;//其它人的宴会数组
	///////////////////////////////////////////////////
	//兑换物品字典
	double resMapUpdateTime:4;//刷新时间（秒）
	dict<int32> resMap:5;//物品字典，格式：resMap[resID]=buyCount;
	///////////////////////////////////////////////////
	//宴会历史
	array<PartyHistroy> partyHistroyArr:6;
	//宴会历史
	array<PartyBreakHistroy> partyBreakHistroyArr:7;
}

//巾帼
struct LadyHero
{
	dict<int8> event17ItemIDMap:1;//event17Item兑换字典（对应json_table/event17Item.json），格式：event17ItemIDMap[id]=1;
}


//json数据
struct Json
{
	Achievement achievement:2;//成就
	DailyTask dailyTask:3;//日常任务
	dict<int32> item:4;//道具列表//item[itemID]=count;
	dict<Wife> wife:5;//红颜列表//wife[wifeID]=Wife();
	WifeRandomFuck wifeRandomFuck:6;//红颜随机宠幸
	Story story:7;//剧情
	MakeRes makeMoney:8;//经营:商业
	MakeRes makeFood:9;//经营:农业
	MakeRes makeSoldier:10;//经营:征兵
	Battle battle:11;//经营:军营
	array<Hero> hero:12;//豪杰列表
	WifePlus wifePlus:13;//红颜加成
	VIP vip:14;//VIP	
	dict<int32> buyMap:15;//商城限购字典//buyMap[shopID]=buyCount
	dict<int32> vipBuyMap:38;//vip商城限购字典//vipBuyMap[shopID]=buyCount
	WaterBattle waterBattle:16;//论战（议政）
	dict<int8> payMap:17;//充值首充字典//payMap[payID]=1;
	Work work:18;//政务
	MainTask mainTask:19;//主线任务
	KingCity kingCity:20;//皇宫
	LimitTask limitTask:21;//限时任务（限时领奖）
	dict<int8> titleMap:22;//称号字典//titleMap[titleID]=0;
	// int32 titleID:23;//当前称号
	// dict<double> clothesMap:24;//时装字典//clothesMap[clothesID]=stageBt;//与活动相关的时装需要记录获取时的阶段开始时间
	int32 clothesID:25;//当前时装
	ChildSys childSys:26;//子嗣系统
	BuildActivity buildActivity:27;//开服活动百废待兴
	Prison prison:28;//牢房系统
	Search search:29;//寻访系统
	VipWeal vipWeal:30;//VIP福利
	Guide guide:31;//新手引导
	Decorate decorate:32;//我家装饰系统
	SomeDayReward someDayReward:33;//7天签到
	BookHouse bookHouse:34;//书院
	GemSys gemSys:35;//珍宝系统
	MyRoad myRoad:36;//为官之道
	TowerBattle towerBattle:37;//过关斩将（塔战）
	Tribute tribute:39;//百姓进贡
	LadyHero ladyHero:40;//巾帼
}

//扩展json数据
struct JsonExt
{
	WaterBattleExt waterBattleExt:1;//论战
	PairSys pairSys:2;//联姻系统
	WorldBoss worldBoss:3;//白夜缉凶系统（世界BOSS）
	CDKey cdKey:4;//CDKEY
	TowerBattleExt towerBattleExt:5;//过关斩将扩展（塔战）
	Party party:6;//宴会
}

//联盟BOSS
struct UnionBoss
{
	// double bt:1;//副本开始时间（秒）
	int32 hp:2;//血量
	int32 hpMax:3;//血量最大值
	int32 point:4;//总积分
	int32 killerUserID:5;//击杀者userID
	string nickname:6;//击杀者昵称
	int32 killerLevel:7;//击杀者等级
	int32 killerHead:8;//击杀者头像
	int32 killerFashionID:9;//击杀者时装
}

//联盟信息
struct UnionJson
{
	dict<int8> userIDMap:1;//成员字典，格式：userIDMap[userID]=0;
	dict<int8> reqUserIDMap:2;//申请加入字典，格式：reqUserIDMap[userID]=0;
	// dict<int8> reqUserIDHistoryMap:3;//申请加入历史字典，格式：reqUserIDHistoryMap[userID]=0;
	dict<UnionBoss> bossMap:4;//boss字典，格式：bossMap[bossID]=UnionBoss();
	dict<int8> viceLeaderUserIDMap:5;//副队长字典，格式：viceLeaderUserIDMap[userID]=0;
	dict<int8> pickUserIDMap:6;//精英字典，格式：pickUserIDMap[userID]=0;
	dict<int8> richmanIDMap:7;//拉拢成功的权贵字典，格式：richmanIDMap[richmanID]=0;
	dict<int8> rewardUserIDMap:8;//联盟冲榜奖励字典，格式：rewardUserIDMap[userID]=0;
}

//联盟权贵信息
struct UnionRichmanJson
{
	dict<int32> unionFriendMap:1;//联盟好感度字典，格式：unionFriendMap[unionID]=friendVal;
}
