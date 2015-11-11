<?php

namespace app\models\news;

use Yii;
use app\models\users\users;
use app\models\users\UsersTournaments;
use app\models\tournaments\Tournaments;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property string $id
 * @property string $subject
 * @property string $body
 * @property string $date
 * @property string $author
 * @property integer $id_tournament
 * @property integer $archive
 *
 * @property Users $author0
 */
class News extends \yii\db\ActiveRecord
{

    const ARCHIVE_TRUE = 1;
    const ARCHIVE_FALSE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */

    public function behaviors() {

        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'author',
                'updatedByAttribute' => 'author',
            ]
        ];
    }
    public function scenarios() {

        $scenarios = parent::scenarios();
        $scenarios['send'] = ['subject', 'body', 'id_tournament'];
        $scenarios['archive'] = ['archive'];

        return $scenarios;
    }

    public function rules()
    {
        return [
            [['subject', 'id_tournament', 'body'], 'required'],
            [['body'], 'string'],
            [['author', 'id_tournament', 'archive'], 'integer'],
            [['subject'], 'string', 'max' => 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Тема',
            'body' => 'Содержание',
            'date' => 'Опубликовано',
            'author' => 'Автор',
            'id_tournament' => 'Турнир',
            'archive' => 'Архив',
        ];
    }

    public function beforeSave($insert) {

        if(parent::beforeSave($insert)) {

            //assigning 'send' scenario to indicate that need to send emails after the news is saved
            if(!Yii::$app instanceof \yii\console\Application && Yii::$app->request->post('send'))
                $this->scenario = 'send';

            if(Yii::$app instanceof \yii\console\Application) {

                $this->author = ArrayHelper::getValue(users::find()->where(['username' => 'administrator'])->one(), 'id');
            }

            //if scneario is 'archive' do not update the date
            if($this->scenario != 'archive') {
                $this->date = time();
            }
            return true;
        } else {
            return false;
        }
    }

    //sending news emails to users
    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        if($this->scenario == 'send') {

            $this->sendNews();
        }
    }

    //get 5 last active news to display on the main page
    public static function getTopActiveNews() {

        $news = self::find()
            ->where(['archive' => self::ARCHIVE_FALSE])
            ->with(['tournament'])
            ->limit(5)
            ->orderBy(['date' => SORT_DESC])
            ->all();

        return $news;
    }

    private function sendNews() {

        //getting the list of recipients for general news (id_tournament == 0) or specific tournament otherwise
        if($this->id_tournament == 0) {

            $users = Users::find()->select(['email'])->where(['notifications' => Users::SUBSCRIBED, 'active' => Users::STATUS_ACTIVE])->all();
            $recipients = ArrayHelper::getColumn($users, 'email');
            $subject = 'Новости сайта - '.$this->subject;
        } else {

            $users = UsersTournaments::find()
                ->joinWith('idUser')
                ->where([
                    'sf_users_tournaments.notification' => UsersTournaments::NOTIFICATION_ENABLED,
                    'sf_users_tournaments.id_tournament' => $this->id_tournament,
                    'sf_users.active' => Users::STATUS_ACTIVE
                ])
                ->all();
            $recipients = ArrayHelper::getColumn($users, 'idUser.email');
            $subject = "Новости турнира ".Tournaments::findOne($this->id_tournament)->tournament_name.' - '.$this->subject;
        }

        if(!empty($recipients)) {

            foreach($recipients as $one) {

                $messages[] = Yii::$app->mailer->compose('news', [
                    'content' => $this->body,
                ])
                    ->setFrom([Yii::$app->params['adminEmail'] => 'Sportforecast'])
                    ->setTo($one)
                    ->setSubject($subject);
            }

            Yii::$app->mailer->sendMultiple($messages);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor0()
    {
        return $this->hasOne(\app\models\users\Users::className(), ['id' => 'author']);
    }

    public function getTournament()
    {
        return $this->hasOne(\app\models\tournaments\Tournaments::className(), ['id_tournament' => 'id_tournament']);
    }

    //convert archive code to friendly name
    public static function archive($status) {

        return ($status == self::ARCHIVE_FALSE)? 'Активные' : 'В архиве';
    }

    //getting possible statuses
    public static function getStatuses() {

        return [
            self::ARCHIVE_TRUE => 'В архиве',
            self::ARCHIVE_FALSE => 'Активные'
        ];
    }

    public function setArchive() {

        if($this->isAttributeChanged('archive')){
            $this->scenario = 'archive';
            $this->save();
        }
    }

    public static function listDropDownPrep() {

        return ['0' => 'Новости сайта'] + ArrayHelper::map(Tournaments::find()->where(['or', ['is_active' => Tournaments::GOING], ['is_active' => Tournaments::NOT_STARTED]])->all(), 'id_tournament', 'tournament_name');
    }

    public static function tournamentFilter() {

        $tournaments = News::find()->select('id_tournament')->orderBy('id_tournament', 'decs')->with('tournament')->asArray()->distinct()->all();

        foreach($tournaments as $tournament) {
            if($tournament['id_tournament'] == 0) {
                $tournamentFilter[0] = 'Новости сайта';
            } else
                $tournamentFilter[$tournament['id_tournament']] = $tournament['tournament']['tournament_name'];
        }

        return $tournamentFilter;
    }

    public static function activeTournamentFilter() {

        $tournaments = News::find()->select('id_tournament')->orderBy('id_tournament', 'decs')->with('tournament')->where(['archive' => self::ARCHIVE_FALSE])->asArray()->distinct()->all();

        $tournamentFilter['all'] = 'Все новости';
        foreach($tournaments as $tournament) {
            if($tournament['id_tournament'] == 0) {
                $tournamentFilter[0] = 'Новости сайта';
            } else
                $tournamentFilter[$tournament['id_tournament']] = $tournament['tournament']['tournament_name'];
        }

        return $tournamentFilter;
    }

    public static function authorFilter() {

        $authors = News::find()->select('author')->with('author0')->asArray()->distinct()->all();
        foreach($authors as $author)
            $authorFilter[$author['author']] = $author['author0']['username'];

        return $authorFilter;
    }
}
