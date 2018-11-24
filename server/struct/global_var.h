//全局变量结构体（对应global_var表）

//活动信息
struct RushLoopActivity
{
	int32 id:1;//活动ID
	double bt:2;//活动开始时间（秒）
	double dt:3;//活动持续时间（秒）
	double rt:4;//活动奖励时间（秒）
	// int8 sign:5;//活动开始标记：0=未开始；1=开始；
}

//阶段信息（对应rushLoop配置表）
struct RushLoop
{
	double bt:1;//阶段开始时间（秒）
	double dt:2;//阶段持续时间（秒）
	int32 stage:3;//阶段ID
	dict<RushLoopActivity> activityMap:4;//活动字典activityMap[activityID]=new RushLoopActivity();
	//上次活动信息字典（记录上次活动阶段开始时间）
	dict<double> oldActivityMap:5;//oldActivityMap[activityID]=stageBt;
}

//皇宫王爷信息
struct KingCityWang
{
	int32 userID:1;//用户ID
	string chat:2;//个性宣言
}

//皇宫系统信息
struct KingCity
{
	KingCityWang wang1:1;//势力王爷
	KingCityWang wang2:2;//论战王爷
	KingCityWang wang3:3;//亲密王爷
	KingCityWang wang4:4;//帮会王爷
	KingCityWang wang5:5;//xx王爷
}

//开服活动百废待兴
struct BuildActivity
{
	double buildVal:1;//建设值（每天清0）
	string updateDate:2;//更新日期，格式：2017-10-24
	int8 sendMailComplete:3;//发送奖励邮件完成标记：0=否；1=是；
}

//游戏服信息
struct GameServer
{
	string bt:1="2017-01-01 00:00:00";//开服时间，格式：2017-10-01 00:00:00
	int8 switch:2;//开服开关
	int8 isResetStage:5;//是否重置阶段为0
	int32 serverID:3;//游戏服ID（开服时设定）
	string serverIP:4;//游戏服IP（开服时设定）
}

//世界BOSS
struct WorldBoss
{
	string updateDate:1;//更新日期，格式：2017-10-24
	int32 hp:2;//世界BOSS血量
	int32 hpMax:6;//世界BOSS血量最大值
	int32 point:3;//BOSS总积分
	int32 killerUserID:4;//击杀者userID
	string nickname:5;//击杀者昵称
	int32 killerLevel:8;//击杀者等级
	int32 killerHead:9;//击杀者头像
	int32 killerFashionID:12;//击杀者时装
	int8 reward:7;//发邮件奖励标记：0=未发；1=已发；
	int32 unionID:10;//联盟ID
	string unionName:11;//联盟名称
	//
	int8 daySendMsg:13=0;//日场发送世界公告标记
	int8 nightSendMsg:14=0;//夜场发送世界公告标记
	//
	int32 level:15=0;//BOSS等级，对应json_table/worldBossKiller.json中day（0=需要被初始化）
}

//联盟
struct Union
{
	string updateDate:1;//更新日期，格式：2017-10-24
	// int32 unionAllocID:2=10001;//联盟ID分配//不再使用
}

//议政日榜
struct WaterBattleDayRank
{
	string updateDate:1;//更新日期，格式：2017-10-24
}

//全局变量定义（全部成员会导入global_var表）
struct Var
{
	GameServer gameServer:1;//游戏服信息
	KingCity kingCity:2;//皇宫系统信息
	RushLoop rushLoop:3;//循环冲榜（对应rushLoop配置表）
	BuildActivity buildActivity:4;//开服活动百废待兴
	WorldBoss worldBoss:5;//世界BOSS
	Union union:6;//联盟
	WaterBattleDayRank waterBattleDayRank:7;//议政日榜
}