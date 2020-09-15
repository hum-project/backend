import React, {Component} from "react";
import {connect} from "react-redux";

class AnswersItem extends Component {

    render() {
        let number = this.props.contentReducer.numOfAnswers;
        let template = this.props.contentReducer.translation.answersHeading;
        let replaceIndex = template.indexOf('[');
        let heading = template.substring(0, replaceIndex) + number + template.substring(replaceIndex+2);
        if (number > 1) {
            heading += 's';
        }

        let questions = [...this.props.contentReducer.questions];
        let answeredQuestions = questions.filter(question => question.answer !== null);
        let hiddenInputs = answeredQuestions.map(question => {
            let key = "answer_" + question.id;
           return <input type={"hidden"} id={key} name={key} key={key} value={question.answer}/>;
        });

        return (
            <form id={this.props.id ? this.props.id : ""} className="content-item center-align" method="post">
                <div className="item-header center">
                    <h2 className="bold">{heading}</h2>
                </div>
                <p>{this.props.contentReducer.translation.answersContent}</p>
                {hiddenInputs}
                <button className="answer-option">{this.props.contentReducer.translation.answersButton}</button>
            </form>
        );

    }

}

const mapStateToProps = state => ({
    ...state
});

export default connect(mapStateToProps)(AnswersItem);