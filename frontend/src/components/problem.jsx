import React, { Component } from "react";
import {
    Link,
    Redirect
} from "react-router-dom";
// import { Markdown } from 'react-showdown';
import MarkdownRender from "./markdown.js";
import "./hover.css";
import load from "./load.svg";

class Problem extends Component {
    constructor(props) {
        super(props);
        this.state = {

        };
    }

    render_submissions() {
        if (this.props.submissions.status === "NO") {
            return <div>Loading...</div>;
        }

        return (
            this.props.submissions.result.data.content.slice(0, 8).map(submission => { return (<div >{submission.username}({submission.score} points)</div>) })
        );
    }

    render_problem() {

        const submitStyle = {
            margin: 0,
            top: "43%",
            left: "47.5%",
            padding: 0,
            background: "black",
            border: "none",
            width: "5%",
            height: "10%",
            position: "fixed",
            outlineWidth: 0,
            overflow: "auto"
        };

        if (this.props.problem_details.status === "NO") {
            if (this.props.problem_code.localeCompare("error:nothing") === 0) {
                return <Redirect to="/" />
            }
            return (
                <input type="image" style={submitStyle} src={load} alt="Loading" />
            )
        }
        else {
            let data = this.props.problem_details.result.data.content.body;
            data = data.replace(/`/g, "");
            data = data.replace(/###/g, "");

            return (
                <div>
                    <h1 style={{ marginBottom: "10%" }}><b>{this.props.problem_details.result.data.content.problemName} ({this.props.problem_code})</b></h1>
                    <MarkdownRender source={data} />
                </div>
            )
        }
    }

    render() {
        const buttonStyle = {
            top: "0%",
            backgroundColor: "black" /* Green */,
            border: "2.5 px solid black",
            height: "10%",
            color: "white",
            padding: "15px 32px",
            textAlign: "center",
            textDecoration: "none",
            display: "inline-block",
            fontWeight: "Bold",
            marginBottom: "10%",
            marginLeft: "10%",
            fontSize: 24,
            outlineWidth: 0
        };

        const buttonStyle2 = {
            top: "0%",
            backgroundColor: "black" /* Green */,
            border: "2.5 px solid black",
            height: "10%",
            color: "white",
            padding: "15px 32px",
            textAlign: "center",
            textDecoration: "none",
            display: "inline-block",
            fontWeight: "Bold",
            marginBottom: "10%",
            marginLeft: "15%",
            fontSize: 24,
            outlineWidth: 0
        };

        return (
            <div>
                <div style={{ marginLeft: "5%" }} className="dropdown">
                    <button style={{
                        fontWeight: "Bold", fontSize: 24, color: "white", backgroundColor: "black", border: "2.5 px solid black",
                        height: "10%"
                    }} className="dropbtn">Recent Submissions</button>
                    <div className="dropdown-content">
                        {this.render_submissions()}
                    </div>
                </div>

                <Link to="/submit">
                    <button style={buttonStyle} type="button">
                        Make Submission
                </button>
                </Link>
                
                <Link to="/tags">
                    <button style={buttonStyle2} type="button">
                        Add Tags
                </button>
                </Link>
                
                <div style={{}}>
                    < div style={{ paddingRight: "25%", paddingLeft: "5%", top: "30%" }}>
                        {this.render_problem()}
                    </div>
                </div >
            </div>
        );
    }
}

export default Problem;
